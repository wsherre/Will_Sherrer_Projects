clc;
rng default;
points_amount = 100;
x = reshape(linspace(0, 5, 26), 26, 1);
y = reshape(linspace(20, 25, 26), 26, 1);
X_1 = [x x;y y];
outliers = [100 100];
X_2 = cat(1, X_1, outliers);

[idx1, C1] = kmeans(X_1, 2);
[idx2, C2] = kmeans(X_2, 2);
[idx3, C3] = kmedoids(X_1, 2);
[idx4, C4] = kmedoids(X_2, 2);

h = figure;
plot(C1(:,1), C1(:,2), '.', 'MarkerSize',15);
hold on 
plot(C2(:,1), C2(:,2), '.', 'MarkerSize',15);
plot(C3(:,1), C3(:,2), '.', 'MarkerSize',15);
plot(C4(:,1), C4(:,2), '.', 'MarkerSize',15);
l = legend('K-means no outliers', 'K-means outliers', 'K-medoids no outliers', 'K-medoids outliers');
l.Location = 'southeast';
xlim([0, 120])
ylim([0, 120])
title('K-Means vs K-Median Centroid Location')
hold off
waitfor(h)
