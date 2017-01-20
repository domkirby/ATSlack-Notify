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
*/
//SET THESE VARIABLES
//CHMOD this to 0600

//Global Variables
$wsdl = "https://webservices5.autotask.net/ATServices/1.5/atws.wsdl"; #WEB SERVICES ENDPOINT THE NUMBER MATCHES THE REALM NUMBER#
$slacknotificationsendpoint = "https://hooks.slack.com/services/WHOLE BUNCHA DATA HERE"; #YOUR SLACK ENDPOINT#
$atzone = "ww5"; #your autotask realm get this by logging in and checking your url https://ww5.autotask.net (between :// and .autotask.net is the realm)#
$username = ""; #autotask api username
$password = ""; #autotask api password
$extensiontoken = ""; #This String is used for all ticket extensions (append as ?s=THISTOKEN)

//Database Configuration (We use a database to map Slack users to Autotask Resources)
$dbhost = "localhost"; #MySQL Server
$dbusername = "username"; #MySQL User (should have all perms on DB, DO NOT USE ROOT)
$dbpassword = "password"; #MySQL Password
$dbname = "atslack"; #database name
$dbmantoken = "RANDOMSTRONG"; #token from your slash command (you will get this from Slack)
$adminlist = "admin1|admin2"; //Separate by pipe symbol as seen in example if you need multiple people to have access. (list of users allowed to manage db)

//timeoutfix
//if you are having timeout issues, set this to TRUE
$timeoutfix = false;


//ticketSlack2.php (Ticket Notifications)
$ticketnotificationroom = "tickets"; #Slack room that you want the messages in, minus the hashtag#

//ticketReply.php (Ticket Reply Notifications)
$replyenabled = true; #Set this to false to prevent this function from being fired ever

//ticketLookup.php (/lookup to lookup a ticket's details)
$ticketlookuptoken = ""; //Slack token from /lookup slash command


/*
TESTING MODE


When test mode is enabled, the script will display the data in the browser insted of pushing it tl SLACK.

When test mode is set to true, the data will not be sent to Slack but will instead be displayed in the browser.

*/
$testmode = false;