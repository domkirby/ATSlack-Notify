<?php
/* 	
	ATSlack
    Copyright (C) 2016  domkirby
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>. 
	
	ticketReply.php - Alerts the ticket owner via Slack when a customer replies to the ticket.
	You should not need to edit this file, just edit config.php with the appropriate variables.
*/
#Get Lib, functions, config
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/autoload.php';
#Set variables for ticket number and id#
$ticketNumber = $_POST['number'];
$ticketId = $_POST['id'];
require_once __DIR__ . '/functions.php';
#Check to see if the owner disabled this
if(!$replyenabled) { die("Disabled"); }
##########################################################
####THIS FUNCTION IS IMPORTANT TO PREVENT DATA LEAKAGE####
##########################################################
if(!($_GET['s'] == $extensiontoken)) {
	die("Invalid Token or No Token Received");
}
# Now that we've checked security, we'll do some real work
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
$ResourceUsername = $ticketData["ResourceUsername"];
//Fire PullReplyResourceSlackName to get the resource's name
$ResourceSlackArray = PullReplyResourceSlackName($ResourceUsername,$dbhost,$dbusername,$dbpassword,$dbname);
$ResourceSlackName = $ResourceSlackArray["slackuser"];
if($ResourceSlackName == "") {
	//if we couldn't find the slack user we'll send this to the ticket notification room from config.php (this happens if the ticket is unassigned or if the user isn't mapped in the database
	$Recipient = "#".$ticketnotificationroom;
	$resourcenotfound = true;
} else {
	//if we found the user in the DB, we'll DM him or her with the $message
	$Recipient = "@".$ResourceSlackName;
	$resourcenotfounce = false;
}
//Fire MakeSlackNewTicketReplyMessage to get an encoded message for Slack
$message = MakeSlackNewTicketReplyMessage($ticketNumber,$ticketId,$ticketTitle,$ContactName,$ContactPhone,$ContactEmail,$companyName,$atzone,$resourcenotfound);
#if testmode, display in browser
if($testmode){
	echo urldecode($message)."<br />";
	echo "<br />".$ResourceUsername."<br>".$Recipient;
	echo "<BR>".$slacknotificationsendpoint;
	echo "<br /><br /><br /><br /><br />";
	print_r($ticketData);
}
else {
	slack($message,$Recipient,$slacknotificationsendpoint);
}
?>