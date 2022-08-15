function [ beta, costHistory ] = gradient_ridge( A, y, beta, lambda, learningRate, repetition, beta_truth)

    costHistory = zeros(repetition, 1);
    for i = 1:repetition       
    
        derivative = (A' * (A * beta - y)) + (2 * lambda * beta); 

        beta = beta - learningRate * derivative;

        %MSE loss
        costHistory(i) = sum((beta - beta_truth).^2);
    
    end
end