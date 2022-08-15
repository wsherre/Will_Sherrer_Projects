clear;clc;
for i=1:2
    if i == 1
        A = imread("3.png");
    else
        A = imread("Lenna.png");
    end
    A = imnoise(A, 'salt & pepper', .2);
    h = figure();
    imshow(A)
    waitfor(h);
    if i == 1
        imwrite(A,"3_noise.png");
    else
        imwrite(A,"Lenna_noise.png");
    end
    
    k=50;
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
    siz1 = size(IR(:, 1));
    siz1 = siz1(1);
    siz2 = size(IR(1, :));
    siz2 = siz2(2);
    q = zeros(siz1, siz2, 3);
    q(:, :, 1) = c1;
    q(:, :, 2) = c2;
    q(:, :, 3) = c3;
    h = figure();
    imshow(q)
    waitfor(h);
    if i == 1
        imwrite(q, "3_reduced_noise.png");
    else
        imwrite(q,"Lenna_reduced_noise.png");
    end
end