close all; clc
A = imread("Lenna.png");

for k= [2 5 20 50 80 100]
    R = A(:, :, 1);
    G = A(:, :, 2);
    B = A(:, :, 3);
    
    IR = im2double(R);
    IG = im2double(G);
    IB = im2double(B);
    
    [u1,s1,v1] = svd(IR);
    [u2,s2,v2] = svd(IG);
    [u3,s3,v3] = svd(IB);
 
    c1 = zeros(size(IR));
    c2 = zeros(size(IG));
    c3 = zeros(size(IB));
    
    c1 = u1(:, 1:k) * s1(1:k, 1:k) * v1(:, 1:k)';
    c2 = u2(:, 1:k) * s2(1:k, 1:k) * v2(:, 1:k)';
    c3 = u3(:, 1:k) * s3(1:k, 1:k) * v3(:, 1:k)';
    
    q(:, :, 1) = c1;
    q(:, :, 2) = c2;
    q(:, :, 3) = c3;
    h = figure;
    imshow(q)
    waitfor(h);
end

