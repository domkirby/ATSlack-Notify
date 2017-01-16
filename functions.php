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
#GLOBAL FUNCTIONS
#SLACK Function: Does the Slack Stuff
function slack($message, $room, $slackurl) {
        $room = ($room) ? $room : "#general";
        $data = "payload=" . json_encode(array(
                "channel"       =>  $room,
                "text"          =>  $message,
            ));
	
	// You can get your webhook endpoint from your Slack settings
        $ch = curl_init($slackurl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
	
#End Slack Function
#GetTicketInfo - New Master Function to Get Ticket Info

function GetTicketInfo($TheticketNumber, $wsdl, $username, $password) {
	$authWsdl = $wsdl;
	$opts = array('trace' => 1);
	$client = new ATWS\Client($authWsdl, $opts);
	$zoneInfo = $client->getZoneInfo($username);
	$authOpts = array(
		'login' => $username,
		'password' => $password,
		'trace' => 1,   // Allows us to debug by getting the XML requests sent
	);
	$wsdl = str_replace('.asmx', '.wsdl', $zoneInfo->getZoneInfoResult->URL);
	$client = new ATWS\Client($wsdl, $authOpts);
	//Ticket Object Query (the root of all things)
	$ticketquery = new ATWS\AutotaskObjects\Query('Ticket');
	$ticketNumberField = new ATWS\AutotaskObjects\QueryField('ticketnumber');
	$ticketNumberField->addExpression('Equals',$TheticketNumber);
	$ticketquery->addField($ticketNumberField);
	//get the ticket
	$ticket = $client->query($ticketquery);
	// Create TicketEntities
	$TicketEntities = $ticket->queryResult->EntityResults->Entity;
	// Now we get the ticket ContactID, and query against that for the Ticket Contact
	$contactId = $TicketEntities->ContactID;
	$cquery = new ATWS\AutotaskObjects\Query('Contact');
	$contactidfield = new ATWS\AutotaskObjects\QueryField('id');
	$contactidfield->addExpression('Equals',$contactId);
	$cquery->addField($contactidfield);
	//get the contact
	$contact = $client->query($cquery);
	// Create ContactEntities
	$ContactEntities = $contact->queryResult->EntityResults->Entity;
	//Now we get the ticket AccountID, and query against that to get the company name
	$AccountId = $TicketEntities->AccountID;
	$aquery = new ATWS\AutotaskObjects\Query('Account');
	$accountidfield = new ATWS\AutotaskObjects\QueryField('id');
	$accountidfield->addExpression('Equals',$AccountId);
	$aquery->addField($accountidfield);
	//get the account
	$account = $client->query($aquery);
	// Create AccountEntities
	$AccountEntities = $account->queryResult->EntityResults->Entity;
	//Now we get the ticket AssignedResourceID, and query against that to get the company name
	$AssignedResourceID = $TicketEntities->AssignedResourceID;
	$rquery = new ATWS\AutotaskObjects\Query('Resource');
	$ResourceIdField = new ATWS\AutotaskObjects\QueryField('id');
	$ResourceIdField->addExpression('Equals',$AssignedResourceID);
	$rquery->addField($ResourceIdField);
	//get the Resource
	$AssignedResource = $client->query($rquery);
	// Create ResourceEntities
	$ResourceEntities = $AssignedResource->queryResult->EntityResults->Entity;
	// Now we create each piece of data as a var
	$TicketTitle = $TicketEntities->Title;
	$FirstName = $ContactEntities->FirstName;
	$LastName = $ContactEntities->LastName;
	$TheName = $FirstName." ".$LastName;
	$Phone = $ContactEntities->Phone;
	$Email = $ContactEntities->EMailAddress;
	$AccountName = $AccountEntities->AccountName;
	$ResourceUsername = $ResourceEntities->UserName;
	//Now we put this data into an array, and return that array so that ticketSlack can make a message
	$ticketDataArray = [
		"TicketTitle" => $TicketTitle,
		"ContactName" => $TheName,
		"ContactPhone" => $Phone,
		"ContactEmail" => $Email,
		"CompanyName" => $AccountName,
		"ResourceUsername" => $ResourceUsername,
		"AccountId" => $AccountId
		];
	return $ticketDataArray;
}
#END GLOBAL
#NEW TICKET NOTIFICATION FUNCTIONS
#MakeSlackNewTicketMessage - Create the slack notification text and encode it for the JSON response
function MakeSlackNewTicketMessage($ticketNumber,$ticketId,$title,$name,$phone,$email,$company,$atzone) {
	$link = "https://".$atzone.".autotask.net/Autotask/AutotaskExtend/ExecuteCommand.aspx?Code=OpenTicketDetail&TicketID=".$ticketId;
	$message = "New Ticket Created: ".$ticketNumber."\n Title: ".$title."\nCompany: ".$company."\nContact Name & Phone: ".$name." | ".$phone."\n Email: ".$email."\n <".$link."|View Ticket>";
	$slackReady = urlencode($message);
	return $slackReady;
}
#End MakeMessage
#END NEW TICKET NOTIFICATION FUNCTIONS

#TICKET REPLY NOTIFICATION FUNCTIONS

#THIS FUNCTION GETS THE SLACK USERNAME FOR THE AUTOTASK RESOURCE
function PullReplyResourceSlackName($ResourceUsername,$dbhost,$dbusername,$dbpassword,$dbname) {
	$query = "SELECT slackuser FROM usermap WHERE atusername = '$ResourceUsername'";
	$mysql = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname); //Connect MySQL
	if (!$mysql) //Check for errors
	{
		die("Connection Error: " . mysqli_connect_error());
	}
    if(mysqli_query($mysql,$query))
    {
        $result = mysqli_query($mysql,$query);
		$row = mysqli_fetch_array($result);
    }
	return $row;
}
# END SLACK NAME

# THIS FUNCTION MAKES A SLACK READY MESSAGE
function MakeSlackNewTicketReplyMessage($ticketNumber,$ticketId,$title,$name,$phone,$email,$company,$atzone,$resourcenotfound) {
	$link = "https://".$atzone.".autotask.net/Autotask/AutotaskExtend/ExecuteCommand.aspx?Code=OpenTicketDetail&TicketID=".$ticketId;
	$message = "New Reply From Customer On: ".$ticketNumber."\n Title: ".$title."\nCompany: ".$company."\nContact Name & Phone: ".$name." | ".$phone."\n Email: ".$email."\n <".$link."|View Ticket>";
	if($resourcenotfound) $message .= "\nTICKET UNASSIGNED OR RESOURCE IS NOT MAPPED TO SLACK USER";
	$slackReady = urlencode($message);
	return $slackReady;
}
#END TICKET REPLY NOTIFICATION FUNCTIONS