#import fid as FID
from matplotlib import image
import torch
import torch.nn as nn
import torch.utils.data
import torch.optim as optim
import numpy as np
import torchvision.transforms as transforms
import torchvision.datasets as datasets
from torchvision.utils import save_image
from models import *
import sys
import time
import datetime
import matplotlib.pyplot as plt

device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')

def show(img):
    npimg = img.detach().numpy()
    plt.imshow(np.transpose(npimg, (1,2,0)), interpolation='nearest')
    plt.show()

def sample_image(n_row, epochs_done, data_loader):
    """Saves a grid of generated digits ranging from 0 to n_classes"""
    
    #generate real images
    images = torch.Tensor([])
    real_data = iter(data_loader)
    img1,_ = next(real_data)
    img2,_ = next(real_data)
    images = torch.cat((img1, img2))
    save_image(images.data, "acgan_result/real/%s.png" % str(epochs_done), nrow=10, normalize=True)

    # Sample noise
    z = torch.FloatTensor(np.random.normal(0, 1, (n_row ** 2, 100)))
    # Get labels ranging from 0 to n_classes for n rows
    labels = np.array([num for _ in range(n_row) for num in range(n_row)])
    labels = torch.LongTensor(labels)
    gen_imgs = generator(z, labels)

    save_image(gen_imgs.data, "acgan_result/real/%s.png" % str(epochs_done), nrow=8, normalize=True)

    #return FID.fid("acgan_result/real/", "acgan_result/fake/", epochs_done - 1, 100, acgan=True)

if __name__ == '__main__':

    #hyper params
    batch_size = 50
    image_size = 64

    transform = transforms.Compose([transforms.Resize(image_size),
                                    transforms.ToTensor(),
                                    transforms.Normalize((0.5, 0.5, 0.5), (0.5, 0.5, 0.5))])

    cifar_dataset = datasets.CIFAR10(root = "./data", download = True, transform = transform)
    data_loader = torch.utils.data.DataLoader(cifar_dataset, batch_size = batch_size, shuffle = True)
    
    #hyper params
    latent_size = 100
    #batch size from utils
    batch_size = batch_size
    learning_rate = 0.0002
    num_epochs = 20
    #classes = ('plane', 'car', 'bird', 'cat', 'deer', 'dog', 'frog', 'horse', 'ship', 'truck')

    generator = AC_Generator().to(device)
    discriminator = AC_Discriminator().to(device)
    generator.apply(weights_init_acgan)
    discriminator.apply(weights_init_acgan)

    try:
        generator.load_state_dict(torch.load("weights/ac_generator_weights.pth"))
        discriminator.load_state_dict(torch.load("weights/ac_discriminator_weights.pth"))
        print("Parameters successfully loaded")
    except:
        print("Cannot Load Model weights")
        
    adversarial_loss = nn.BCELoss()
    auxiliary_loss = nn.CrossEntropyLoss()

    optimizer_gen = optim.Adam(generator.parameters(), lr=learning_rate)
    optimizer_dis = optim.Adam(discriminator.parameters(), lr=learning_rate)
    data_loader = data_loader

    epoch_dis_loss_array = np.array([])
    epoch_gen_loss_array = np.array([])
    epoch_dis_acc_array = np.array([])
    fid_score_array = np.array([])
    try:
        dis_loss_array = np.load("weights/acgan_discriminator_loss.npy")
        gen_loss_array = np.load("weights/acgan_generator_loss.npy")  
        dis_acc_array = np.load("weights/acgan_discriminator_acc.npy")  
        fid_score_array = np.load("weights/acgan_fid_scores.npy")
    except:
        print("Can't Load Previous Loss Record")
    print("TRAINING......")

    dataset_total = len(data_loader)
    training_total = len(data_loader) * num_epochs
    train_start_time = time.perf_counter()
    eta = 0
    total_eta = 0

    fid_score_array = np.append(fid_score_array, sample_image(n_row=10, epochs_done=0, data_loader=data_loader))
    np.save("weights/acgan_fid_scores.npy", fid_score_array)
    for epoch in range(num_epochs):
        epoch_time = time.perf_counter()
        for i, (real_images, labels) in enumerate(data_loader):

            valid = torch.FloatTensor(batch_size, 1).fill_(1.0)
            fake = torch.FloatTensor(batch_size, 1).fill_(0.0)

            #train generator
            real_images = real_images.to(device)
            labels = labels.to(device)

            optimizer_gen.zero_grad()

            z = torch.FloatTensor(np.random.normal(0, 1, (batch_size, latent_size))).to(device)
            gen_labels = torch.LongTensor(np.random.randint(0, 10, batch_size))

            gen_images = generator(z, labels)
            
            validity, pred_label = discriminator(gen_images)
            gen_loss = 0.5 * (adversarial_loss(validity, valid) + auxiliary_loss(pred_label, gen_labels))
            epoch_gen_loss_array = np.append(epoch_gen_loss_array, gen_loss.item())

            gen_loss.backward()
            optimizer_gen.step()

            #train discriminator
            optimizer_dis.zero_grad()

            # Loss for real images
            real_pred, real_aux = discriminator(real_images)
            d_real_loss = (adversarial_loss(real_pred, valid) + auxiliary_loss(real_aux, labels)) / 2

            # Loss for fake images
            fake_pred, fake_aux = discriminator(gen_images.detach())
            d_fake_loss = (adversarial_loss(fake_pred, fake) + auxiliary_loss(fake_aux, gen_labels)) / 2

            # Total discriminator loss
            dis_loss = (d_real_loss + d_fake_loss) / 2
            epoch_dis_loss_array = np.append(epoch_dis_loss_array, dis_loss.item())

            # Calculate discriminator accuracy
            pred = np.concatenate([real_aux.data.cpu().numpy(), fake_aux.data.cpu().numpy()], axis=0)
            gt = np.concatenate([labels.data.cpu().numpy(), gen_labels.data.cpu().numpy()], axis=0)
            dis_acc = np.mean(np.argmax(pred, axis=1) == gt)
            epoch_dis_acc_array = np.append(epoch_dis_acc_array, dis_acc * 100)

            dis_loss.backward()
            optimizer_dis.step()
            

            #bar calculation
            bar_len = 20
            filled_len = int(round(bar_len * i / float(dataset_total)))
            bar = '=' * filled_len + '-' * (bar_len - filled_len)

            #calc percentage done with epoch
            percents = 100.0 * i / float(dataset_total)
            begin_time = time.perf_counter()
            elapsed_time = begin_time - epoch_time

            #calc total completion
            percent_completed = ((dataset_total * epoch) + i) / float(training_total)
            total_elapsed_time = time.perf_counter() - train_start_time


            if percents != 0: 
                eta = (elapsed_time / percents) * (100.0 - percents)
                total_eta = (total_elapsed_time / percent_completed) * (1 - percent_completed)
            else:
                eta = 0
                total_eta = 0

            sys.stdout.write(f' Epoch[{epoch + 1}/{num_epochs}] Iteration %s/%s [%s] %s%%  ETA for epoch: %s Dis Loss: %s, Acc: %.2s%%,  Generator Loss: %s, Total ETA: %s\r' % (i + 1, dataset_total, bar, round(percents, 1), str(datetime.timedelta(seconds=int(eta))), round(dis_loss.item(), 4), 100.0 * dis_acc, round(gen_loss.item(), 4), str(datetime.timedelta(seconds=int(total_eta)))))
            sys.stdout.flush()

                

            #Save the weights every 1/50th of an epoch in case of failure or cancellation
            if (i + 1) % int((len(data_loader) /50)) == 0:
                torch.save(generator.state_dict(), "weights/ac_generator_weights.pth")
                torch.save(discriminator.state_dict(), "weights/ac_discriminator_weights.pth")

        #print at the end of each epoch        
        print(f' Epoch[{epoch + 1}/{num_epochs}] Iteration %s/%s [%s] %s%%  ETA for epoch: %s Dis Loss: %s, Acc: %.2s%%,  Generator Loss: %s, Total ETA: %s\r' % (i + 1, dataset_total, bar, round(percents, 1), str(datetime.timedelta(seconds=int(eta))), round(dis_loss.item(), 4), 100.0 * dis_acc, round(gen_loss.item(), 4), str(datetime.timedelta(seconds=int(total_eta)))))
        dis_acc_array = np.append(dis_acc_array, np.mean(epoch_dis_acc_array))
        dis_loss_array = np.append(dis_loss_array, np.mean(epoch_dis_loss_array))
        gen_loss_array = np.append(gen_loss_array, np.mean(epoch_gen_loss_array))
        np.save("weights/acgan_discriminator_loss.npy", dis_loss_array)
        np.save("weights/acgan_generator_loss.npy", gen_loss_array)
        np.save("weights/acgan_discriminator_acc.npy", dis_acc_array)
        #sample_image(n_row=10, epochs_done=epoch+1, data_loader=data_loader)
        #fid_score_array = np.append(fid_score_array, sample_image(n_row=10, epochs_done=0, data_loader=data_loader))
        #np.save("weights/acgan_fid_scores.npy", fid_score_array)
        
    
              
                



            






            




