clc;
A = [1, 2, 4;1, 3, 5; 1, 7, 7; 1, 8, 9];
y = [1;2;3;4];
b_0 = [0;0;0];
y_hat = A * b_0;
[U, S, V] = svd(A'*A);
sigma_max = max(max(S));
num_iterations = 1;
lambdas = [0.1, 1, 10, 100, 200];
costhistory_array = zeros(num_iterations, length(lambdas));

for i=1:length(lambdas)
    lambda = lambdas(i);
    %compute ground truth
    beta_truth = (A'*A + lambda * eye(3))\ A'*y;

    learning_rate = 1 /(lambda + sigma_max);
    
    [beta, costhistory] = gradient_ridge(A, y, b_0, lambda, learning_rate, num_iterations, beta_truth);
    costhistory_array(:, i) = costhistory;
end
h = figure;
%plot(costhistory_array(:, 1));
hold on
plot(1, costhistory_array(:, 2), '.', 'MarkerSize', 20);
plot(10, costhistory_array(:, 3), '.', 'MarkerSize', 20);
plot(100, costhistory_array(:, 4), '.', 'MarkerSize', 20);
plot(200, costhistory_array(:, 5), '.', 'MarkerSize', 20);
hold off
legend(["1", "10", "100", "200"])
title("Loss of Function per Lambda")
waitfor(h);