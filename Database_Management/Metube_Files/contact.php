<?php
@ob_start();
session_start();
include_once "mysqlClass.inc.php";
include 'function.php';
$GLOBALS['d'] = new dbh();
?>
<?php
if(isset($_POST['accept'])){
    $sql = 'update contacts set apply = 0 where user = ? and contact = ?';
    $val = [$_SESSION['username'], $_POST['accept']];
    $GLOBALS['d']->insert($sql, $val);
}
if(isset($_POST['decline'])){
    $sql = 'delete from contacts where (user = ? and contact = ?) or (contact = ? and user = ?)';
    $val = [$_SESSION['username'], $_POST['decline'], $_SESSION['username'], $_POST['decline']];
    $GLOBALS['d']->delete($sql, $val);
}
if(isset($_POST['send'])){
    $sql = 'insert into contacts values (?, ?, ?)';
    $val = [$_POST['send'], $_SESSION['username'], 1];
    $GLOBALS['d']->delete($sql, $val);
}
if(isset($_POST['confirm'])){
    $sql = 'delete from contacts where (user = ? and contact = ?) or (contact = ? and user = ?)';
    $val = [$_SESSION['username'], $_POST['confirm'], $_SESSION['username'], $_POST['confirm']];
    $GLOBALS['d']->delete($sql, $val);
}
if(isset($_POST['cancel'])){
    unset($_POST['remove']);
}
?>
<html>
<head>
<title>Contacts</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
</head>
<body>
<div id='header'>
<h1>Your Contacts</h1>
</div>
<div id='navbar'>
    <ul class='ul'>
        <li><button class="nav_button" onclick="back('browse.php')">Go to Browse</button></li>
        <li><button class="nav_button" onclick="back('inbox.php')">Go to Inbox</button></li>
        <li><button class="nav_button" name='receive' onclick="show('receive');hide('apply');hide('list');">Show Received Friend Requests</button></li>
        <li><button class="nav_button" name='apply' onclick="show('apply');hide('receive');hide('list');" >Show Sent Friend Requests</button></li>
        <li><button class="nav_button" name='list' onclick="show('list');hide('receive');hide('apply');" >Send Friend Request to User</button></li>
    </ul>
</div>
<div id='inline'>
    <div id="receive" style="display: none;">
        <h3>Received Friend Requests </h3>
        <div id='contact-table-scroll'>
            <table width="100%">
                <tr>
                </tr>
                <tr>
                    <td style="padding: 10px 0px 10px 0px;"><span class="request">Name </span></td>
                    <td style="padding: 10px 0px 10px 0px;"><span class="request">Email </span></td>
                </tr>   
                <?php $contacts = getreceived();
                if (empty($contacts)){
                    echo "<tr><td>No Requests Received</td></tr>";
                }else{
                    foreach($contacts as $c){
                        echo "<tr>";
                        echo "<td>" . $c['contact'] . "</td><td>"; if ($c['email'] != "") { echo $c['email']; } else {echo "No email on file";} echo "</td>";
                        echo "<td><form action='' method='post'><button class='button' name='accept' value='" . $c['contact'] . "'>Accept</button>";
                        echo "<button class='button' name='decline' value='" . $c['contact'] . "'>Decline</button></form></td>";
                        echo "</tr>";
                    }
                }?>
            </table>
        </div>
    </div>

    <div id="apply" style="display: none;">
        <h3>Sent Friend Requests </h3>
        <div id='contact-table-scroll'>
            <table width="100%">
                <tr>
                    <td style="padding: 10px 0px 10px 0px;"><span class="request">Name </span></td>
                    <td style="padding: 10px 0px 10px 0px;"><span class="request">Email </span></td>
                    <td style="padding: 10px 0px 10px 0px;"><span class="request">Status </span></td>
                </tr>   
                <?php $contacts = getrequests();
                if (empty($contacts)){
                    echo "<tr><td>No Contacts</td><td></td></tr>";
                }else{
                    foreach($contacts as $c){
                        echo "<tr>";
                        echo "<td>" . $c['user'] . "</td><td>"; if ($c['email'] != "") { echo $c['email']; } else {echo "No email on file";} echo "</td>";
                        echo "<td>Pending...</td>";
                        echo "<td><form action='' method='post'><button class='button' name='decline' value='" . $c['user'] . "'>Cancel Request</button>";
                        echo "</tr>";
                    }
                }?>
            </table>
        </div>
    </div>
    <div id="list" style="display: none;">
        <h3>Send Friend Request to User </h3>
        <div id='contact-table-scroll'>
            <table width='100%'>
                <tr>
                    <td style="padding: 10px 0px 10px 0px;"><span class="request">Name </span></td>
                    <td style="padding: 10px 0px 10px 0px;"><span class="request">Email </span></td>
                </tr>   
                <?php $contacts = getusers();
                if (empty($contacts)){
                    echo "<tr><td>No Users</td><td></td></tr>";
                }else{
                    foreach($contacts as $c){
                        echo "<tr>";
                        echo "<td>" . $c['username'] . "</td><td>"; if ($c['email'] != "") { echo $c['email']; } else {echo "No email on file";} echo "</td>";
                        echo "<td><form action='' method='post'><button class='button' name='send' value='" . $c['username'] . "'>Send Friend Request</form></button></td>";
                        echo "</tr>";
                    }
                }?>
            </table>
        </div>
    </div>
    

        <table style='width: 100%; margin-top: 10px;'>
            <tr>
                <td style="padding: 0px 0px 10px 0px;"><span class="contact">Contact Name </span></td>
                <td style="padding: 0px 0px 10px 0px;"><span class="contact">Email </span></td>
                <td> </td>
            </tr>   
            <?php $contacts = getcontacts();
            if (empty($contacts)){
                echo "<tr><td>No Contacts</td><td></td></tr>";
            }else{
                foreach($contacts as $c){
                    echo "<tr>";
                    echo "<td>" . $c['username'] . "</td><td>"; if ($c['email'] != "") { echo $c['email']; } else {echo "No email on file";} echo "</td>";
                    if(isset($_POST['remove']) and ($_POST['remove'] == $c['username'])){
                        echo "<td style='white-space: nowrap'><form action='' method='post'><button class='button' style='background-color: #ff0000;' name='confirm' value='" . $_POST['remove'] . "'>Confirm Remove Friend</form></button></td>";
                        echo "<td><form action='' method='post'><button class='red_button' name='cancel'>Cancel</form></button></td>";
                    }
                    else{
                        echo "<td><form action='' method='post'><button class='button' name='remove' value='" . $c['username'] . "'>Remove Friend</form></button></td>";
                    }
                    echo "</tr>";
                }
            }?>
        </table>
</div>
</body>
</html>