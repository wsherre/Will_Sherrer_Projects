function [cost] = objective_p5(x, y)
    cost = 0.5 * norm(x - y, "fro") + norm(svd(x), 1);
end