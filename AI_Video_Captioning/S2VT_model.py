import torch
import torch.nn as nn 
from dictionary import *
device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')


#full seq to seq model that includes encoder and decoder in forward pass for simplicity
class Seq2Seq(nn.Module):
    def __init__(self,batch_size=1, featuremaps=4096, hidden=128, number_of_frames=80, word2id={}, id2word={}):
        super().__init__()
        #parameters
        self.batch_size = batch_size
        self.featuremaps = featuremaps
        self.hidden = hidden
        self.number_of_frames = number_of_frames
        self.max_length = 50

        #vocab, should already be built and lives in dictionary.py
        self.word2id, self.id2word = word2id, id2word
        self.vocab_size = len(self.word2id)

        #encoder linear and lstm
        self.encoderLinear = nn.Linear(featuremaps, hidden)
        self.encoderLSTM = nn.LSTM(self.hidden, self.hidden, batch_first=True)

        #decoder linear and lstm
        self.decoderLSTM = nn.LSTM(2 * self.hidden, self.hidden, batch_first=True)
        self.decoderLinear = nn.Linear(self.hidden, self.vocab_size)

        #embedding from vocab to hidden
        self.embedding = nn.Embedding(self.vocab_size, hidden)

    #status will be if training or not
    def forward(self, video_features: torch.Tensor, caption=None):
        #encode 
        video_features = video_features.contiguous().view(-1, self.featuremaps)
        video_features = self.encoderLinear(video_features)
        video_features = video_features.view(-1, self.number_of_frames, self.hidden)
        #pad features
        padding = torch.zeros([self.batch_size, self.number_of_frames-1, self.hidden]).to(device)
        video_features = torch.cat((video_features, padding), 1)  
        #video output size is [batch_size, 2*number_of_frames - 1, hidden]
        video_output, _ = self.encoderLSTM(video_features)

        #if training, output will look different for cross entropy loss
        if self.training:
            #embed words in caption: size = [batch_size, number_of_frames - 1, hidden]
            caption = self.embedding(caption[:, 0:self.number_of_frames-1])
            
            #pad the caption size after padding is = [batch_size, 2*number_of_frames - 1, 2*hidden]
            padding = torch.zeros([self.batch_size, self.number_of_frames, self.hidden]).to(device)
            caption = torch.cat((padding, caption), 1) 
            caption = torch.cat((caption, video_output), 2) 

            #send through decoder; size after decoder is = [batch_size, 2*number_of_frames - 1, hidden]
            caption_output, states = self.decoderLSTM(caption)

            #reshape; size after reshape is = [batch_size * (number_of_frames - 1), 2*hidden]
            caption_output = caption_output[:, self.number_of_frames:, :]
            caption_output = caption_output.contiguous().view(-1, self.hidden)
            #get output
            caption_output = self.decoderLinear(caption_output)

            #output should be of shape [batch_size*79(number_of_frames - 1), vocab_size] for cross entropy loss calculation
            return caption_output

        else:
            #decode actual caption
            #padd input; size after = [batch_size, number_of_frames, 2*hidden]
            padding = torch.zeros([self.batch_size, self.number_of_frames, self.hidden]).to(device)
            input = torch.cat((padding, video_output[:, 0:self.number_of_frames, :]), 2)

            #get initial states for decoder from encoder output
            _, states = self.decoderLSTM(input)

            #create initial BOS input. This will tell the decoder to start sentence
            bos_id = self.word2id["<BOS>"] * torch.ones(self.batch_size, dtype=torch.long).to(device)
            input = self.embedding(bos_id)

            #add to encoder output and reshape; size after = [batch_size, 1, 2*hidden]
            input = torch.cat((input, video_output[:, self.number_of_frames, :]), 1)
            input = input.view(self.batch_size, 1, 2*self.hidden)

            #get the initial output of the decoder. The first word of the sentence
            output, states = self.decoderLSTM(input, states)
            output = output.contiguous().view(-1, self.hidden)
            output = self.decoderLinear(output)
            output = torch.argmax(output, 1)

            #append first word to caption
            caption = []
            caption.append(output)

            #go in range of the max_length - 2 (BOS, first word already made). Ideally, it shouldn't have to go this far. 
            #If it does max out then the model needs to be trained for longer
            for i in range(self.max_length-2):
                #embed previous word
                input = self.embedding(output)
                #catinate input with video output of encoder. We concatinate with encoder output at time step to maintain some memory --
                # of the features. 
                input = torch.cat((input, video_output[:, self.number_of_frames+1+i, :]), 1)
                input = input.view(self.batch_size, 1, 2*self.hidden)

                #get next output an decoder states
                output, states = self.decoderLSTM(input, states)
                output = output.contiguous().view(-1, self.hidden)
                output = self.decoderLinear(output)
                output = output.argmax(1)
                #add word to sentence
                caption.append(output)

            return caption

    
        

        


