<?php
@ob_start();
session_start();
include_once "mysqlClass.inc.php";
include 'function.php';
$database = new dbh();
$GLOBALS['d'] = $database;


if (isset($_POST['send'])){
    if(isset($_POST['users'])){
        if($_POST['users'] == "disable"){
            $error = "Must select a user to send message to";
        }
        else if ($_POST['message'] == ""){
            $error = "Message must not be empty";
        }
        else{
            $sql = 'insert into messages values (?, ?, ?, NOW())';
            $vals = [$_POST['users'], $_SESSION['username'], $_POST['message']];
            $GLOBALS['d']->insert($sql, $vals);
            $_POST['convo'] = $_POST['users'];
        }
    }
    else{
        $sql = 'insert into messages values (?, ?, ?, NOW())';
        $vals = [$_POST['send'], $_SESSION['username'], $_POST['message']];
        $GLOBALS['d']->insert($sql, $vals);
        $_POST['convo'] = $_POST['send'];
        unset($_POST['send']);
    }
}


$sql = 'select username from account';
$GLOBALS['users'] = $GLOBALS['d']->query($sql);
$sql = 'select * from messages where target = ? or sender = ? order by time asc';
$vals = [$_SESSION['username'], $_SESSION['username']];
$GLOBALS['message_list'] = $GLOBALS['d']->query($sql, $vals);
$contacts = getcontacts();
$con = array();
foreach($contacts as $c){
    array_push($con, $c['username']);
}
$GLOBALS['contact'] = $con;

function get_convo(){
    $arr = array();
    foreach($GLOBALS['message_list'] as $msg){
        if ( ($msg['target'] == $_SESSION['username'] and $msg['sender'] == $_POST['convo'])  or   ($msg['sender'] == $_SESSION['username'] and $msg['target'] == $_POST['convo'])){
            array_push($arr, $msg);
        }
    }
    return $arr;
}
?>

<title>Inbox</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<head>
<div id='header'>
    <h1>Inbox</h1>
</div>
</head>
<body>
    <div id="navbar" style='width: 10%;'>
        <ul class='ul'>
            <li><button class="nav_button" onclick="back('browse.php')">Go to Browse</button></li>
            <li><button class="nav_button" onclick="back('contact.php')">Go to Contacts</button></li>
        </ul>
    </div>

<div id='inline'>
<form method="post" action="inbox.php">
    <table width ="100%">
        <tr>
            <td style="width: 20%;"><?php 
                                if (empty($GLOBALS['message_list'])){
                                    echo "You have no messages";
                                 
                                }
                                else{
                                    $arr = array();
                                    foreach($GLOBALS['message_list'] as $person){
                                        if($person['target'] == $_SESSION['username']){
                                            $name = $person['sender'];
                                            if (!in_array($name, $arr)){
                                                if(!in_array($name, $GLOBALS['contact'])){
                                                    echo "<button class=\"red_button\" type=\"submit\" name=\"convo\" value=\"" . $name . "\">" . $person['sender'] . "</button> <br>";  
                                                    array_push($arr, $name);
                                                }else{
                                                    echo "<button class=\"button\" type=\"submit\" name=\"convo\" value=\"" . $name . "\">" . $person['sender'] . "</button> <br>";  
                                                    array_push($arr, $name);
                                                }
                                            }
                                        }
                                        else{
                                            $name = $person['target'];
                                            if (!in_array($name, $arr)){
                                                if(!in_array($name, $GLOBALS['contact'])){
                                                    echo "<button class=\"red_button\" type=\"submit\" name=\"convo\" value=\"" . $name . "\">" . $person['target'] . "</button> <br>";  
                                                    array_push($arr, $name);
                                                }else{
                                                    echo "<button class=\"button\" type=\"submit\" name=\"convo\" value=\"" . $name . "\">" . $person['target'] . "</button> <br>";  
                                                    array_push($arr, $name);
                                                }
                                            }
                                        }
                                    }
                                }?>
            </td>
            <td width="80%">
                <textarea  class="text" rows="20" cols="100" style='background-color: gray; color: white;' readonly><?php 
                if(isset($_POST['convo'])){
                    echo 'Conversation with: ' . $_POST['convo'] . "&#13;&#10;&#13;&#10;";
                    $convo = get_convo();
                    foreach($convo as $msg){
                        echo $msg['time'] . "  " . $msg['sender'] . ":  " . $msg['message'] . "&#13;&#10;";
                    }
                    echo "</textarea><br>";
                    if(!in_array($_POST['convo'], $GLOBALS['contact'])){
                        echo "<input size=\"100\" class=\"table\" type=\"text\" name=\"message\" value='This person is not in your contacts. To continue the converstaion, add them to your contacts.' style='background-color: gray; color: white;' readonly></input>";
                        echo "<button class=\"red_button\" type='button'>Send</button>";
                    }else{
                        echo "<input size=\"100\" class=\"table\" type=\"text\" name=\"message\" placeholder=\"Send message to " . $_POST['convo'] . "...\"></input>";
                        echo "<button class=\"button\" name=\"send\" value=\"" . $_POST['convo'] . "\" type=\"submit\">Send</button>";
                    }
                } 
                else{
                    echo "No conversation selected";
                    echo "</textarea><br>";
                }
                ?> 
                
                
            </td>
        </tr>     
    </table>
</form>
<form method="post" action="inbox.php"><button class="button" type="submit" name="compose">Compose</button>
<?php
    if(isset($_POST["compose"])){
        echo "<label for=\"users\" style='color: white;'> Send message to</label>";
        echo "<select class='cat_search' style='margin-left: 5px; margin-right: 5px; height: 25px;' name=\"users\" id=\"users\">";
        echo "<option value=\"disable\" selected>Select User</option>";
        foreach (getcontacts() as $user){
            if($user['username'] != $_SESSION['username']){
                echo "<option value=\"" . $user['username'] . "\">" . $user['username'] . "</option>";
            }
        }
        echo "<input size=\"100\" class=\"table\" type=\"text\" name=\"message\" placeholder=\"Send message to user...\"></input>";
        echo "<button class=\"button\" name=\"send\" type=\"submit\">Send</button>";
        unset($_POST['compose']);
    }
?>
<?php
  if(isset($error))
   {  echo "<div id='passwd_result'>".$error."</div>";}
?>
<form>
</div>
</body>
