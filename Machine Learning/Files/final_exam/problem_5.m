clc; clear;

%want to have the same random samples to replicate results
rng('default');

X = rand(100, 100);
Y = rand(100, 100);
lambda = 0.001;
ob = objective_p5(X, Y);
ob_scores = [];
for i=1:1000
    [U,S,V] = svd(X, 0);
    gradient = (X - Y) + U*V';
    X = X - lambda * gradient;
    ob = objective_p5(X, Y);
    %add objective to array for graph
    ob_scores = [ob_scores ob];
end

h = figure();
plot(ob_scores);
ylabel("Objective Score")
xlabel("Iterations")
title("Objective per iteration")
legend(["Objective"]);
saveas(gcf, 'ob_min.png')
waitfor(h);
