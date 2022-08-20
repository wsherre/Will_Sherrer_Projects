clc;
A = [1, 2, 4;1, 3, 5; 1, 7, 7; 1, 8, 9];
y = [1;2;3;4];
b_0 = [0;0;0];
y_hat = A * b_0;
[U, S, V] = svd(A'*A);
sigma_max = max(max(S));
learning_rate = 1 /(sigma_max);
num_iterations = 10000;
%compute least squares
beta_truth = (A'*A)\(A'*y);

[beta, costhistory] = gradient_least(A, y, b_0, learning_rate, num_iterations, beta_truth);

h = figure;
plot(costhistory);
title("Loss of Function per Iterations")
waitfor(h);



