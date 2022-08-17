# 8430 HW2 Video Caption

## If program starts training then set num_epochs to 0 on line 40 in model_seq2seq.py

Run program by using shell script sh hw2_seq2seq.sh 'data_directory' 'output_file'

Data Directory should be MSVD folder which should match the hierarchy;

MSVD Folder<br>
|<br>
-->training_data<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> feats (folder)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> video (folder)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> id.txt<br>
-->testing_data<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> feats (folder)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> video (folder)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> id.txt<br>
--> training_label.json<br>
--> testing_label.json<br>


Model Implementation
![LSTM_MODEL_IMAGE](/image/LSTM_model_image.png)