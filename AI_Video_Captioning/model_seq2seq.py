from dictionary import *
from S2VT_model import Seq2Seq
import json
import torch
import torch.nn as nn
import numpy as np
import sys
from utils import *
device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')


if __name__ == "__main__":


    #get files from shell script
    if(len(sys.argv)) > 1:
        MSVD_DIR = sys.argv[1]
        OUTPUT_FLIE = sys.argv[2]
    else:
        MSVD_DIR = "MSVD"
        OUTPUT_FLIE = "output.txt"
    
    #create dictionaries from the json files located in MSVD folder
    try:
        with open(MSVD_DIR + "/training_label.json") as json_file:
            training_dict = json.load(json_file)
    except:
        print(f"ERROR OPENING TRAINING JSON AT LOCATION: {MSVD_DIR + '/training_label.json'}")
        sys.exit()

    try:
        with open(MSVD_DIR + "/testing_label.json") as json_file:
            testing_dict = json.load(json_file)
    except:
        print(f"ERROR OPENING TESTING JSON AT LOCATION: {MSVD_DIR + '/testing_label.json'}")
        sys.exit()

    
    #hyperparamters
    num_epochs = 0
    batch_size = 10
    num_of_frames = 80
    feature_map_size = 4096
    hidden_nodes = 128
    learning_rate = 0.0001
    word_count_threshold = 3
    wordamount, unk_required = build_vocabulary(word_count_threshold, MSVD_DIR=MSVD_DIR)
    word2id, id2word = word_to_ids(wordamount, unk_required)
    model_params_location = "model_weights/model_train_params_100.pth"

    #model
    model = Seq2Seq(batch_size, feature_map_size, hidden_nodes, num_of_frames, word2id, id2word).to(device)
    criterion = nn.CrossEntropyLoss()
    optimizer = torch.optim.Adam(model.parameters(), lr=learning_rate)
    model.train()


    if num_epochs > 0:
        print("TRAINING.........")
    else:
        print("SKIPPING TRAINING........")

    #can load pretrain weights and set training epoch to 0 for quicker output. These weights were trained for only 1 epoch
    try:
        model.load_state_dict(torch.load(model_params_location))
        print("Loaded Model Paramters Successfully")
    except:
        print("Model Paramters are different or can not load pretrained parameters. Will begin Training from scratch")

    step_size = int(len(training_dict)/batch_size)
    #train model
    for epoch in range(num_epochs):
        
        loss_array = []
        #loop through training dictionary and randomly shuffle the dictionary
        #random.shuffle(training_dict)
        for i in range(step_size):
            
            video_features, captions, caption_masks = fetch_training_data(training_dict, batch_size, MSVD_DIR, word2id)
            video_features, captions, caption_masks = np.stack(video_features, axis=0), np.stack(captions, axis=0), np.stack(caption_masks, axis=0)
            video_features, captions, caption_masks = torch.FloatTensor(video_features).to(device), torch.LongTensor(captions).to(device), \
                                                        torch.FloatTensor(caption_masks).to(device)

        
            #forward pass, mask caption output
            caption_output = model(video_features, captions)
            cap_labels = captions[:, 1:].contiguous().view(-1)       
            cap_mask = caption_masks[:, 1:].contiguous().view(-1) 

            #calculate loss with mask added. Mask is to help the model learn how long a sentence should be. 
            l_loss = criterion(caption_output, cap_labels)
            m_loss = l_loss * cap_mask
            loss = torch.sum(m_loss)/torch.sum(cap_mask)

            #optimize
            optimizer.zero_grad()
            loss.backward()
            optimizer.step()

            #record loss
            loss_array.append(loss.item())
            
            #print every 100 iterations
            if ((i + 1) % int(step_size/5) == 0):
                loss = np.median(loss_array)
                loss_array = []
                print(f'Epoch: {epoch + 1}/{num_epochs} Step: {i + 1}/{step_size} Loss: {loss:.4f}')

        #save every 2 epochs in case I have to stop during training or computer shuts down
        if (epoch + 1) % 2 == 0:
            torch.save(model.state_dict(), model_params_location)
            print("Saved Model Paramters Successfully At Checkpoint")

    #save parameters
    if num_epochs > 0:
        torch.save(model.state_dict(), model_params_location)

    print("OUTPUTING.........")
    #open output file to write
    try:
        output_file = open(OUTPUT_FLIE, "w")
    except:
        print(f"ERROR IN OPENING FILE: {OUTPUT_FLIE}")
        sys.exit()

    #loop through testing dictionary
    batch_size = 1
    model = Seq2Seq(batch_size, feature_map_size, hidden_nodes, num_of_frames, word2id, id2word).to(device)
    model.load_state_dict(torch.load(model_params_location))
    model.eval()
    for video in testing_dict:
        
        id = video['id']

        feats = str(id) + '.npy'
        feats = np.load(MSVD_DIR + "/testing_data/feat/" + feats)
        video_features = torch.FloatTensor(feats).to(device)

        #get caption for video features
        output_captions = model(video_features)

        captions = []
        for tensor in output_captions:
            captions.append(tensor.tolist())

        # change the format of the captions to a single array
        captions = [row[0] for row in captions]

        #create the caption from ids
        captions = create_caption(captions, id2word)
        
        #add to file
        try:
            output_file.write(str(id) + "," + captions + "\n")
        except:
            print(f"ERROR IN WRITING FILE: {OUTPUT_FLIE}")
            sys.exit()

