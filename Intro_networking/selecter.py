import selectors
from socket import *

sel = selectors.DefaultSelector()

client1 = socket(AF_INET, SOCK_STREAM)
client1.connect(('newton.computing.clemson.edu', 3604))
events = selectors.EVENT_READ | selectors.EVENT_WRITE
print('connected1')
data = '1'
client1.setblocking(False)
sel.register(client1, events, data)

client2 = socket(AF_INET, SOCK_STREAM)
client2.connect(('newton.computing.clemson.edu', 3604))
print('connected2')
data = '2'
client2.setblocking(False)
sel.register(client2, events, data)

client3 = socket(AF_INET, SOCK_STREAM)
client3.connect(('newton.computing.clemson.edu', 3604))
print('connected3')
data = '3'
client3.setblocking(False)
sel.register(client3, events, data)


while True:
    msg = input(">>>")
    

    events = sel.select(timeout=1)
    for key, mask in events:
        sock = key.fileobj
        data = key.data

        if mask & selectors.EVENT_READ:
            rsp = sock.recv(2048)
            print(data + ': ' + rsp.decode())

        if mask & selectors.EVENT_WRITE:
            sock.send(msg.encode())
        

client1.close()
sel.close()