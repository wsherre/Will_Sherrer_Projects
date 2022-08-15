class MessageParser(object):
    def __init__(self):
        pass
    
    # This function should return a list of dictionaries. Each element in the list represents a separate 
    # message that has been parsed. Each dictionary represents the different elements of that message:
    # the prefix, the command, and the list of params. An example of the data structure to be returned 
    # is shown below:
    # [message1, message2, message3], where
    # message1 =
    # {
    #   "prefix": "theshire.nz",
    #   "command": "SERVER",
    #   "params": ["rivendale.nz", 1, "Home of the Hobbits"]
    # }
    # message2 =
    # {
    #   "prefix": None,
    #   "command": "SERVER",
    #   "params": ["minastirith.nz", 1, "Tower of Guard"]
    # }
    # message3 =
    # {
    #   "prefix": None,
    #   "command": "QUIT",
    #   "params": None
    # }

    # When processing data received from a socket, it's possible that the data contains multiple commands
    # We need to split the message into a list of commands, which are delimited by \r\n
    def parse_data(self, recv_data):
        msg_list = []
        msg = recv_data.decode()
        msg = msg.split('\r\n')
        for m in msg:
            if m != '':
                msg_dict = {}
                msg_dict['prefix'] = None
                msg_dict['command'] = None
                msg_dict['params'] = []
                
                if m.split(":")[0] == '':
                    s = m.split(":", 1)
                    pre_command = s[1]
                    msg_dict['prefix'] = pre_command.split(" ")[0]
                    msg_dict['command'] = pre_command.split(" ")[1] 
                    if len(m.split(" ", 2)) == 3:
                        m = m.split(" ", 2)[2]
                    else:
                        msg_dict['params'] = None  
                else:
                    get_command = m.split(" ", 1)
                    msg_dict['command'] = get_command[0]
                    if len(get_command) > 1:
                        m = get_command[1]
                    else:
                        msg_dict['params'] = None
                
                if not msg_dict['params'] == None:
                    
                    if len(m.split(":")) == 2:
                        mul_params = m.split(":")
                        if mul_params[0] == '':
                            msg_dict['params'].append(mul_params[1])
                        else:
                            leading_params = mul_params[0]
                            leading_params = leading_params.split(" ")
                            for p in leading_params:
                                if not p == '':
                                    msg_dict['params'].append(p)

                            msg_dict['params'].append(mul_params[1])

                    else:
                        leading_params = m.split(" ")
                        for p in leading_params:
                            msg_dict['params'].append(p)

                msg_list.append(msg_dict)
        return msg_list