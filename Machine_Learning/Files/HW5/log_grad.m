function GG=log_grad(y, X, B) 

%compute gradient 

K = size(B, 2);

GG=zeros(size(B));
for i=1:size(X, 1)
    x=0;
    for j=1:K
        w_t = exp(dot(B(:, j), X(i, :)));
        x = x + w_t;
    end
    x=x+1;
    for k=1:K
        GG(:,k)=GG(:, k) + ((y(i)==k) - exp(dot(B(:,k),X(i,:)))/x) * X(i,:)';
    end
end