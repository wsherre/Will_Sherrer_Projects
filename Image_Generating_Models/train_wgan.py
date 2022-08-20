import torch
import torchvision.transforms as transforms
import torchvision.datasets as datasets
import torch.utils.data
import torch.optim as optim
import numpy as np
import torchvision.utils as vutils
from models import *
import fid as FID
import sys
import time
import datetime
device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')

def main():

    #hyper params
    batch_size = 64
    image_size = 64

    transform = transforms.Compose([transforms.Resize(image_size),
                                    transforms.ToTensor(),
                                    transforms.Normalize((0.5, 0.5, 0.5), (0.5, 0.5, 0.5))])

    cifar_dataset = datasets.CIFAR10(root = "./data", download = True, transform = transform)
    data_loader = torch.utils.data.DataLoader(cifar_dataset, batch_size = batch_size, shuffle = True)
    
    #hyper params
    latent_size = 100
    #batch size from utils
    learning_rate = 0.00005
    num_epochs = 11
    #classes = ('plane', 'car', 'bird', 'cat', 'deer', 'dog', 'frog', 'horse', 'ship', 'truck')

    generator = Generator().to(device)
    discriminator = W_Discriminator().to(device)
    generator.apply(weights_init_wgan)
    discriminator.apply(weights_init_wgan)
    
    

    try:
        generator.load_state_dict(torch.load("weights/W_generator_weights.pth"))
        discriminator.load_state_dict(torch.load("weights/W_discriminator_weights.pth"))
        print("Parameters successfully loaded")
    except:
        print("Cannot Load Model weights")
        

    optimizer_gen = optim.RMSprop(generator.parameters(), lr=learning_rate)
    optimizer_dis = optim.RMSprop(discriminator.parameters(), lr=learning_rate)

    dis_loss_array = np.array([])
    gen_loss_array = np.array([])
    fid_score_array = np.array([])
    try:
        dis_loss_array = np.load("weights/wgan_discriminator_loss.npy")
        gen_loss_array = np.load("weights/wgan_generator_loss.npy")  
        fid_score_array = np.load("weights/wgan_fid_scores.npy")
    except:
        print("Can't Load Previous Loss Record")
    print("TRAINING......")
    dataset_total = len(data_loader)
    training_total = len(data_loader) * num_epochs
    train_start_time = time.perf_counter()
    eta = 0
    total_eta = 0
    generator_best_loss = 100

    for epoch in range(num_epochs):
        epoch_time = time.perf_counter()
        for i, (real_images, _) in enumerate(data_loader):

            for param in discriminator.parameters():
                param.data.clamp_(-0.01,0.01)
            discriminator.zero_grad()

            #train on real images
            real_images.to(device)
            real_labels = torch.ones(real_images.size()[0]).to(device)
            out = discriminator(real_images)
            loss_real = torch.mean(out)

            #train with generated image
            noise = torch.randn(real_images.size()[0], latent_size, 1, 1).to(device)
            fake_images = generator(noise)
            fake_labels = torch.zeros(real_images.size()[0]).to(device)
            out = discriminator(fake_images.detach())
            loss_fake = torch.mean(out)

            #calc total loss and update weights of the discriminator
            loss_dis = loss_fake - loss_real
            loss_dis.backward()
            optimizer_dis.step()
            dis_loss_array = np.append(dis_loss_array, loss_dis.item())


            #update weights for the generator
            generator.zero_grad()
            target = torch.ones(real_images.size()[0]).to(device)
            out = discriminator(fake_images)

            loss_gen = -torch.mean(out)
            loss_gen.backward()
            optimizer_gen.step()
            gen_loss_array = np.append(gen_loss_array, loss_gen.item())
            

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
            sys.stdout.write(f' Epoch[{epoch + 1}/{num_epochs}] Iteration %s/%s [%s] %s%%  ETA for epoch: %s Dis Loss: %s, Generator Loss: %s, Total ETA: %s\r' % (i + 1, dataset_total, bar, round(percents, 1), str(datetime.timedelta(seconds=int(eta))), round(loss_dis.item(), 4), round(loss_gen.item(), 4), str(datetime.timedelta(seconds=int(total_eta)))))
            sys.stdout.flush()

            #get best loss per epoch
            if loss_gen.item() < generator_best_loss:
                generator_best_weights = generator.state_dict()
                generator_best_loss = loss_gen.item()

            #Save the weights every 1/50th of an epoch in case of failure or cancellation
            if (i + 1) % int((len(data_loader) /50)) == 0:
                torch.save(generator.state_dict(), "weights/W_generator_weights.pth")
                torch.save(discriminator.state_dict(), "weights/W_discriminator_weights.pth")
                np.save("weights/wgan_discriminator_loss.npy", dis_loss_array)
                np.save("weights/wgan_generator_loss.npy", gen_loss_array)

        #print at the end of each epoch        
        print(f' Epoch[{epoch + 1}/{num_epochs}] Iteration %s/%s [%s] %s%%  ETA for epoch: %s Dis Loss: %s, Generator Loss: %s, Total ETA: %s\r' % (i + 1, dataset_total, bar, round(percents, 1), str(datetime.timedelta(seconds=int(eta))), round(loss_dis.item(), 4), round(loss_gen.item(), 4), str(datetime.timedelta(seconds=int(total_eta)))))
        vutils.save_image(real_images, 'wgan_result/real/real_samples_epoch_{}.png'.format(epoch + 29), normalize = True)
        generator.load_state_dict(generator_best_weights)
        fake = generator(noise)
        vutils.save_image(fake.data, 'wgan_result/fake/fake_samples_epoch_{}.png'.format(epoch + 29), normalize = True)
        generator_best_loss = 100
        fid_score = np.append(fid_score,FID.fid("wgan_result/real/", "wgan_result/fake/", i))

if __name__ == '__main__':
    main()
              
                



            




