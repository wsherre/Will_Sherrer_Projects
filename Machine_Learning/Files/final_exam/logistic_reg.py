import numpy as np
import time

#objective
def f(beta, X, Y):
    return np.ravel(np.ones(len(Y))*(np.log(1+np.exp(X*beta)))-Y.T*X*beta)[0]

#first derivative of objective
def derivative_grad_f(beta, X, Y):
    return X.T*(1/(1+1/np.exp(X*beta))-Y)

#SGD is different in vanilla gradient descent in that it randomly chooses a batch of x rather than use the whole dataset. 
#The final said that the batch_size = 1
def derivative_SGD_f(beta, X, Y, m):
    i = np.random.randint(0, m)
    return X[i, :].T*(1/(1+1/np.exp(X[i, :]*beta))-Y[i, :])

#second derivative of objective
def second_derivative_f(beta, X, Y):  
    return X.T*(np.diag(np.ravel(np.exp(X*beta)/np.power(1+np.exp(X*beta),2)))*X)

if __name__ == "__main__":

    #create data arrays
    objective_gd_100 = []
    objective_new_100 = []
    objective_sgd_100 = []
    time_gd_100 = []
    time_new_100 = []
    time_sgd_100 = []

    objective_gd_1000 = []
    objective_new_1000 = []
    objective_sgd_1000 = []
    time_gd_1000 = []
    time_new_1000 = []
    time_sgd_1000 = []

    size_array = [100, 1000]
    for i in range(len(size_array)):

        m = size_array[i]
        X = np.matrix(np.random.rand(m, 2) * 10)
        Y = np.matrix(np.random.randint(0, 2, (m, 1)))
        beta = np.array([[0.], [0.]])
        learning_rate = 0.001

        t = time.time()
        counter = 0
        #gradient descent
        for i in range(m):
            counter += 1
            beta = beta - learning_rate*derivative_grad_f(beta, X, Y)
            learning_rate = learning_rate/1.02
            if m == 100:
                objective_gd_100.append(f(beta, X, Y))
                time_gd_100.append(time.time() - t)
            else:
                objective_gd_1000.append(f(beta, X, Y))
                time_gd_1000.append(time.time() - t)

        print('iter =',counter)
        print(beta)
        print('norm =',np.linalg.norm(derivative_grad_f(beta, X, Y)))
        print("\n")

        beta = np.array([[0.], [0.]])
        counter = 0
        t = time.time()
        #newton's method
        for i in range(m):
            counter += 1
            beta = beta -  np.linalg.inv(second_derivative_f(beta, X, Y))*derivative_grad_f(beta, X, Y)
            if m == 100:
                objective_new_100.append(f(beta, X, Y))
                time_new_100.append(time.time() - t)
            else:
                objective_new_1000.append(f(beta, X, Y))
                time_new_1000.append(time.time() - t)
        
        print('iter =',counter)
        print(beta)
        print('norm =',np.linalg.norm(derivative_grad_f(beta, X, Y)))
        print("\n")

        counter = 0
        beta = np.array([[0.], [0.]])       
        t = time.time()
        learning_rate = 0.001
        #Stochastic Gradient Descent
        for i in range(m):
            counter += 1
            beta = beta - learning_rate*derivative_SGD_f(beta, X, Y, m)
            #lower the learning rate over time to prevent jumping
            learning_rate = learning_rate/1.02
            if m == 100:
                objective_sgd_100.append(f(beta, X, Y))
                time_sgd_100.append(time.time() - t)
            else:
                objective_sgd_1000.append(f(beta, X, Y))
                time_sgd_1000.append(time.time() - t)

        print('iter =',counter)
        print(beta)
        print('norm =',np.linalg.norm(derivative_SGD_f(beta, X, Y, m)))
        print("\n")

        if m == 100:
            np.save("data/objective_gd_100.npy", np.array(objective_gd_100))
            np.save("data/objective_new_100.npy", np.array(objective_new_100))
            np.save("data/objective_sgd_100.npy", np.array(objective_sgd_100))
            np.save("data/time_gd_100.npy", np.array(time_gd_100))
            np.save("data/time_new_100.npy", np.array(time_new_100))
            np.save("data/time_sgd_100.npy", np.array(time_sgd_100))
        else:
            np.save("data/objective_gd_1000.npy", np.array(objective_gd_1000))
            np.save("data/objective_new_1000.npy", np.array(objective_new_1000))
            np.save("data/objective_sgd_1000.npy", np.array(objective_sgd_1000))
            np.save("data/time_gd_1000.npy", np.array(time_gd_1000))
            np.save("data/time_new_1000.npy", np.array(time_new_1000))
            np.save("data/time_sgd_1000.npy", np.array(time_sgd_1000))