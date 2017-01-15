<?php
/* ticketSlack2.php - NOW MORE FUNCTIONY */
/* STOP EDITING
#
#
#FILL OUT config.php
#
#EDIT AT YOUR OWN RISK
#
*/
//Get required files and stuff
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/autoload.php';
$ticketNumber = $_POST['number'];
$ticketId = $_POST['id'];
require_once __DIR__ . '/functions.php';
//check for testmode variable
if($_POST['testmode']=="yes") {
	define("TESTMODE",true);
}
else {
	define("TESTMODE",false);
}
#I WANT YOU TO USE SSL ~~ Comment this part out at your own risk
if (empty($_SERVER['HTTPS'])) {
    die("SSL WAS NOT USED <br />We want you to use SSL for your own good. Please go back and use SSL");
}
#end ssl check
//Fire GetTicketInfo to get our array of data
$ticketData = GetTicketInfo($ticketNumber,$wsdl,$username,$password);
//Unwrap the array
$ticketTitle = $ticketData["TicketTitle"];
$ContactName = $ticketData["ContactName"];
$ContactPhone = $ticketData["ContactPhone"];
$ContactEmail = $ticketData["ContactEmail"];
$companyName = $ticketData["CompanyName"];
//Fire MakeSlackNewTicketMessage to get an encoded message for Slack
$message = MakeSlackNewTicketMessage($ticketNumber,$ticketId,$ticketTitle,$ContactName,$ContactPhone,$ContactEmail,$companyName,$atzone);
##TESTMODE is created from the checkbox in form.html. It stops the message from being dispatched to Slack but displayes it in the browser.
if(TESTMODE){
	echo urldecode($message)."<br />";
	echo $room."<br />";
	echo $slacknotificationsendpoint;
}
else {
	slack($message,$ticketnotificationroom,$slacknotificationsendpoint);
}
?>