import json
import re
import numpy as np


#build vocab
def build_vocabulary(word_count_threshhold = 5, MSVD_DIR="MSVD"):

    wordamount = {}
    unk_required = False

    unk_required = False
    with open (MSVD_DIR + "/training_label.json") as j:
        trainingLabels = json.load(j)

    #iterate over each training and testing labels
    for index in trainingLabels:
        captions = index['caption']
        #for each sentence in the dictionary
        for sentence in captions:  
            #add BOS and EOS so model will learn when to start and stop                 
            sentence = str("<BOS> " + sentence + " <EOS>")    
            #each word in the sentence          
            for word in sentence.split(' '):        
                if len(word) > 0 and word[0] != '<':
                    word = re.sub(r'[^a-zA-Z]', '', word)
                # Add each unique word to dictionary and increment count for repeat words
                if word not in wordamount:
                    wordamount[word] = 1
                else:
                    wordamount[word] += 1

    #get rid of words that don't meet threshold requirement
    for word in list(wordamount): 
        if wordamount[word] < word_count_threshhold:
            wordamount.pop(word)
            unk_required = True
    return wordamount, unk_required

def word_to_ids(wordamount, unk_requried=False):
    word2id = {}
    id2word = {}
    count = 0
    if unk_requried:
        word2id['<UNK>'] = count
        id2word[count] = '<UNK>'
        count += 1
    for word in wordamount:
        word2id[word] = count
        id2word[count] = word
        count += 1
    return word2id, id2word

#encode sentence from sentence to indexes
def encode_sentence(captions, word2id: dict, maxlength = 80):
    
    if type(captions) == "str":
        captions = [captions]
    caps, cap_mask = [], []

    for cap in captions:
        #get length of caption
        nWord = len(cap.split(' ')) + 1
        #pad caption with EOS at the end 
        cap = '<BOS> ' + cap + ' <EOS>'*(maxlength-nWord)
        #add a caption mask
        cap_mask.append([1.0]*nWord + [0.0]*(maxlength-nWord))
        caption_id = []
        #for each word in caption
        for word in cap.split(' '):
            #strip to just letters
            if len(word) > 0 and word[0] != '<':
                word = re.sub(r'[^a-zA-Z]', '', word)
            #if in the vocab, add it, else add unknown word0
            if word in word2id:
                caption_id.append(word2id[word])
            else:
                caption_id.append(word2id["<UNK>"])
        caps.append(caption_id)

    return np.array(caps), np.array(cap_mask)

#return the words from the indexes of caption input
def create_caption(caption, id2word):
    output_caption = [id2word[word] for word in caption]
    if '<EOS>' in output_caption:
        output_caption = output_caption[0:output_caption.index('<EOS>')]
    output_caption = ' '.join(output_caption)
    return output_caption
