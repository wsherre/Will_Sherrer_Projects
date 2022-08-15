close all;
data = load('housing.data');
x = data(:, 1:13);
y = data(:, 14);
[n, d] = size(x);
seed = 2; rand('state',seed); randn('state', seed);
perm = randperm(n); % remove any possible ordering fx
x = x(perm,:); y = y(perm);

n_array = [25,50,75,100,150,200,300];
train_array = [];
test_array = [];
for i=1:length(n_array)
    Ntrain = n_array(i);
    Xtrain = zscore(x(1:Ntrain,:)); ytrain = y(1:Ntrain);
    Xtest = zscore(x(Ntrain+1:end,:)); ytest = y(Ntrain+1:end);
    offset = mean(ytrain);
    beta = (Xtrain' * Xtrain) \ (Xtrain' * ytrain);
    
    y_train_results = Xtrain * beta + offset;
    y_test_results = Xtest * beta + offset;
    
    train_mse = sum((y_train_results - ytrain).^2)/Ntrain;
    tests_mse = sum((y_test_results - ytest).^2)/(n-Ntrain);
    train_array = [train_array train_mse];
    test_array = [test_array tests_mse];
end

plot(n_array, train_array, 'r-o', 'LineWidth', 2, 'MarkerSize', 4);
hold on
plot(n_array, test_array, 'k--x');
legend('Train', "Test");
ylabel('MSE');
xlabel('Size of training set');
hold off






