array = [10,100,1000,2000];
s = 4;
lambda = 1;
n = 100;
time_arr = [];
for i=1:4
   p = array(1, i);
   A = rand(n, p);
   t1 = clock();
   R_regression = (A' * A + lambda * eye(p)) * A';
   t2 = clock();
   delta_t = t2 - t1;
   %get the seconds
   time = delta_t(1, 6);
   time_arr(end+1) = time;
end
plot(array, time_arr, 'b-o')
ylabel("Time")
xlabel("Lambda")
title("Ridge Regression with variable lambdas")
set(gca, 'Xscale', 'log')