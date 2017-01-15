<?php
define('TOKEN',$_GET['s']);
?>
<html>
<head>
</head>
<body>
<form action="ticketSlack2.php?s=<?php echo TOKEN; ?>" method="POST">
<p>Ticket ID (from ticket url): <input type="text" name="id"></p>
<p>Ticket Number (T20170101.0001): <input type="text" name="number"></p>
<p><input type="submit" value="Send Test" /></p>
<p><b>Note:</b>You need to use an actual existing ticket to do this.</p>
<p>If you want the output to display in the browser, you need to set test mode to true in config.php</p>
</form>
</body>
</html>