close all;
data = load('housing.data');
x = data(:, 1:13);
y = data(:, 14);
[n, d] = size(x);
seed = 2; rand('state',seed); randn('state', seed);
perm = randperm(n); % remove any possible ordering fx
x = x(perm,:); y = y(perm);
lambdas = [logspace(0,4)];
x = zscore(x);
beta_array = [];
for i=1:length(lambdas)
    lambda = lambdas(i);
    beta = lassoAlg(x, y, lambda);
    beta_array = [beta_array beta];
end

plot(log10(lambdas), beta_array, 'LineWidth', 1);
ylabel('Beta Coefficeints');
xlabel('Lambda');
title("Lasso Beta vs Lambda")