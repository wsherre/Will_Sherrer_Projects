clc;
rng(1)
positive = [[1,1];[2,2];[2,3]];
negative = [[3,2];[3,3];[4,4]];
data = [positive; negative];
value = [1;1;1;2;2;2];
x = rand(50000, 2) * 5;


k_list = [1,3];

for i = 1:2
    k = k_list(i);
    [pred, idx, acc] = KNN(k, data, value, x);
    
    class_1 = [];
    class_2 = [];
    for i=1:length(pred)
        if pred(i) == 1
            class_1 = [class_1; x(i, 1:2)];
        else
            class_2 = [class_2; x(i, 1:2)];
        end
    end
    
    h= figure;
    %scatter(data(:, 1), data(:, 2),500, '.');
    hold on 
    scatter(class_1(:, 1), class_1(:, 2), 50, '.');
    scatter(class_2(:, 1), class_2(:, 2), 50, '.');
    xlim([0, 5])
    ylim([0, 5])
    t = "K = " + string(k);
    title(t)
    hold off
    waitfor(h)
end