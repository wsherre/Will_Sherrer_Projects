clc; clear;
a = [[1,3];[2,5];[3,4];[4,3];[5,2];[5,1]];

M = mean(a);
lower_bound = 0;
upper_bound = 6;

a_new = a - M;
C = cov(a)
[U,S,V] = svd(C);
points = a * U(:, 1)
slope_of_projection_line = (U(2,1) - U(1,1))/2;

x = [lower_bound, upper_bound];
y = slope_of_projection_line * x;


h = figure;
scatter(a(:, 1), a(:, 2));
hold on
plot(x, y)
xlim([lower_bound, upper_bound])
ylim([lower_bound, upper_bound])
line([0 0], ylim);
line(xlim, [0 0]); 
title("Projection line of PCA")
hold off
waitfor(h)


