close all;
data = load('housing.data');
x = data(:, 1:13);
y = data(:, 14);
[n, d] = size(x);
seed = 2; rand('state',seed); randn('state', seed);
perm = randperm(n); % remove any possible ordering fx
x = x(perm,:); y = y(perm);
new_x = degexpand(x, 6);
[n, p] = size(new_x);

lambdas = [logspace(-10, 10, 10)];
Ntrain = 300;
Xtrain = zscore(new_x(1:Ntrain,:)); ytrain = y(1:Ntrain);
Xtest = zscore(new_x(Ntrain+1:end,:)); ytest = y(Ntrain+1:end);
train_array = [];
test_array = [];

for i=1:length(lambdas)
    lambda = lambdas(i);
    offset = mean(ytrain);
    beta = (Xtrain' * Xtrain + lambda * eye(p)) \ (Xtrain' * ytrain);
    
    y_train_results = Xtrain * beta + offset;
    y_test_results = Xtest * beta + offset;
    
    train_mse = sum((y_train_results - ytrain).^2)/Ntrain;
    tests_mse = sum((y_test_results - ytest).^2)/(n-Ntrain);
    train_array = [train_array train_mse];
    test_array = [test_array tests_mse];
end

plot(log10(lambdas), test_array, 'k--x');
hold on
plot(log10(lambdas), train_array, 'r-o', 'LineWidth', 2, 'MarkerSize', 4);
legend('Test', "Train");
ylabel('MSE');
xlabel('Lambda');
title("Lambda")