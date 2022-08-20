clc; clear;
x_lower_lim = 0;
x_upper_lim = 6;
a = [[1,3];[2,5];[3,4];[4,3];[5,2];[5,1]];
%classes for data
M = mean(a);
%center data on axis
a_new = a - M;
a_val = [4;2];

[W, Y] =LDA(a_new, a_val);

slope = (Y(6) - Y(1))/5;
x = [x_lower_lim, x_upper_lim];
y = slope * x;


h = figure;
scatter(a(:, 1), a(:, 2));
hold on
%plot([-3, -2, -1, 1, 2, 3], Y)
plot(x, y);
xlim([x_lower_lim, x_upper_lim])
ylim([x_lower_lim, x_upper_lim])
line([0 0], ylim);
line(xlim, [0 0]); 
title("Projection Line of LDA")
hold off
waitfor(h)