clc; clear;

rng('default');

X = rand(100, 9);
y = rand(100, 1);
beta = rand(9, 1);
learning_rate = 0.0001;
ob_scores = [];
t = y - X * beta;
for i=1:100
    grad = beta - 2 * X' * t + 0.5 * sign(beta);
    beta = beta - learning_rate * grad;
    t = y - X * beta;
    ob = (norm(t)^2) + (0.5 * norm(beta)^2) + (0.5 * norm(beta, 1));
    ob_scores = [ob_scores ob];
end

h = figure();
plot(ob_scores)
xlabel("Iterations")
ylabel("Objective Score")
title("Elastic Net Objective per Iteration")
legend(["Objective"])
saveas(gcf, "elastic_net_ob.png")
waitfor(h)