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
require_once __DIR__ . "/config.php";
define('TOKEN',$_GET['s']);
if(!(TOKEN == $extensiontoken)) die("Security violation don't forget the token");
?>
<html>
<head>
 <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<title>Test ATSlack</title>
</head>
<body>
<?php if(!$testmode) echo "<div class=\"alert alert-warning\" role=\"alert\">You are not in test mode. Your messages will be sent to Slack</div>";?>
<h3>Test New Ticket Notification</h3>

<div class="alert alert-info">
<form action="ticketSlack2.php?s=<?php echo TOKEN; ?>" method="POST">
<p>Ticket ID (from ticket url): <input type="text" name="id"></p>
<p>Ticket Number (T20170101.0001): <input type="text" name="number"></p>
<p><input type="submit" value="Send Test" class="btn btn-primary" /></p>
</form>
<p><b>Note:</b>You need to use an actual existing ticket to do this.</p>
</div>
<br><br>
<h3>Test New Ticket Reply Notification</h3>
<div class="alert alert-info">
<form action="ticketReply.php?s=<?php echo TOKEN; ?>" method="POST">
<p>NOTE that this ticket should be assigned to someone that has already been mapped with /usermap</p>
<p>Ticket ID (from ticket url): <input type="text" name="id"></p>
<p>Ticket Number (T20170101.0001): <input type="text" name="number"></p>
<p><input type="submit" value="Send Test" class="btn btn-primary" /></p>
<p><b>Note:</b>You need to use an actual existing ticket to do this.</p>
</form>
</div>
</body>
</html>