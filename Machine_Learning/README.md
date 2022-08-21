This contains the reaserch paper that my partner and I wrote as well as the
final exam for the course. These are included to show the level of problem solving
that went into the course. I can say that this was the hardest final of my time at Clemson.

We researched the different levels of image compression and whether 2  different
Deep Learning models could  be trained on different levels of image compression. 

Abstract
```
Our group explores the effectiveness of Convolutional Neural Networks (CNN) when images that
have been reduced by Principal Component Analysis (PCA) have been input as training. If CNN
models can still be trained on the dimensionally-reduced dataset, it would allow for dataset
expansion without needing to store as much information per image. We use the Canadian Institute
for Advanced Research (CIFAR) dataset for our training and testing images. We then used PCA to
reduce the dataset at different intervals and train 2 CNN architectures on the dimensionallyreduced
images. We used compression ratios at 81.25%, 87.5%, 93.75%, and 96.88% to see whether
they could effectively train the models to classify the images. What we found is that there is a
correlation between the amount of compression and the models’ ability to learn how to classify the
images. At many intervals, 81.2% of image compression, or the first 6 of 32 principal components of
the image, was on par or just below the ability of the standard dataset. We conclude that it is
possible to train models with some form of image compression. However further studies should be
conducted for fine-tuning the technique.
```

The files folder contains the homeworks and MATLAB files
used in the homeworks included to show some of the problem solving I had to do.
The PDFs in the folders are only file of note to read containing answers to the homeworks.
