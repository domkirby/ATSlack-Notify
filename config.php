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

//ticketSlack2.php (Ticket Notifications)
$ticketnotificationroom = "tickets"; #Slack room that you want the messages in, minus the hashtag#
$ticketslacktoken = ""; #Set this to a random string, and be sure to include it on your ticket extension#

/*
TESTING MODE


When test mode is enabled, the script will display the data in the browser insted of pushing it tl SLACK.

When test mode is set to true, the data will not be sent to Slack but will instead be displayed in the browser.

*/
$testmode = false;