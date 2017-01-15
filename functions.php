<?php
#NOTIFICATION FUNCTIONS ticketSlack2.php
#SLACK Function: Does the Slack Stuff
function slack($message, $room, $slackurl) {
        $room = ($room) ? $room : "tickets";
        $data = "payload=" . json_encode(array(
                "channel"       =>  "#{$room}",
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
	// Now we create each piece of data as a var
	$TicketTitle = $TicketEntities->Title;
	$FirstName = $ContactEntities->FirstName;
	$LastName = $ContactEntities->LastName;
	$TheName = $FirstName." ".$LastName;
	$Phone = $ContactEntities->Phone;
	$Email = $ContactEntities->EMailAddress;
	$AccountName = $AccountEntities->AccountName;
	//Now we put this data into an array, and return that array so that ticketSlack can make a message
	$ticketDataArray = [
		"TicketTitle" => $TicketTitle,
		"ContactName" => $TheName,
		"ContactPhone" => $Phone,
		"ContactEmail" => $Email,
		"CompanyName" => $AccountName
		];
	return $ticketDataArray;
}
#MakeSlackNewTicketMessage - Create the slack notification text and encode it for the JSON response
function MakeSlackNewTicketMessage($ticketNumber,$ticketId,$title,$name,$phone,$email,$company,$atzone) {
	$link = "https://".$atzone.".autotask.net/Autotask/AutotaskExtend/ExecuteCommand.aspx?Code=OpenTicketDetail&TicketID=".$ticketId;
	$message = "New Ticket Created: ".$ticketNumber."\n Title: ".$title."\nCompany: ".$company."\nContact Name & Phone: ".$name." | ".$phone."\n Email: ".$email."\n <".$link."|View Ticket>";
	$slackReady = urlencode($message);
	return $slackReady;
}
#End MakeMessage
#END NOTIFICATION FUNCTIONS