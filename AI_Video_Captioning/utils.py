import numpy as np
from dictionary import encode_sentence

def fetch_training_data(training_dict: dict, batch_size=3, MSVD_DIR="MSVD", word2id={}):
    
    video_features = []
    captions = []

    #randomly select training videos of batch size
    videos = np.random.choice(training_dict, batch_size)
    for i in range(batch_size):
        vid = videos[i]
        id = vid['id']
        #randomly select caption
        captions.append(np.random.choice(vid['caption'], 1).tolist()[0])

        feats = str(id) + '.npy'
        feats = np.load(MSVD_DIR + "/training_data/feat/" + feats)
        video_features.append(feats)
    captions, caption_masks = encode_sentence(captions, word2id)

    # video features will have shape [batch_size, 80, 4096]
    # captions and captions masks will have shape [batch_size, 80]
    return video_features, captions, caption_masks
    
