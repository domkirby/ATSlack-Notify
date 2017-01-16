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
require_once __DIR__ . '/config.php';
?>
<html lang="en">
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <title>ATSlack DB Installer</title>
</head>
<body>
<div class="container">
<div class="jumbotron">
<h3>ATSlack Database Setup</h3>
<p>This tool should only be run once you've setup the database settings in config.php!</p>

</div>
<?php
if($_POST['Proceed']=="true") {
	$mysql = mysqli_connect($dbhost, $dbusername, $dbpassword);
	if (!$mysql) {
                    echo "<div class=\"alert alert-danger\" role=\"alert\">";
                    echo "Connection Error: " . mysqli_connect_error();
                    echo "<br />Check config.php</div>";
					echo "<form action=\"installdb.php\">
							<input type=\"hidden\" name=\"Proceed\" value=\"true\" />
							<input type=\"submit\" name='page' class=\"btn btn-primary\" value=\"Retry MySQL\" />
							</form>";
					die();
                }
	$dbselect = mysqli_select_db($mysql, $dbname);
	if (!$dbselect) {
		echo "<div class=\"alert alert-danger\" role=\"alert\">";
		echo "DB Selection Error: " . mysqli_connect_error() . "<br />Check config.php. You must create the database on your own.</div>";
					echo "<form action=\"installdb.php\">
							<input type=\"hidden\" name=\"Proceed\" value=\"true\" />
							<input type=\"submit\" name='page' class=\"btn btn-primary\" value=\"Retry MySQL\" />
							</form>";
		die();
	}
	$sql = "CREATE TABLE IF NOT EXISTS usermap (slackuser VARCHAR(25) PRIMARY KEY, atusername VARCHAR(25) NOT NULL)";
	if (mysqli_query($mysql, $sql)) {
		echo "<div class=\"alert alert-success\" role=\"alert\">";
        echo "Setup Complete";
        echo "</div>";
		echo "<div class=\"alert alert-warning\" role=\"alert\">";
		echo "DELETE THIS FILE. Not doing so causes a security issue!!!";
        die();
	} else {
		echo "<div class=\"alert alert-danger\" role=\"alert\">";
        echo "usermap Table Creation Error: " . mysqli_error($mysql);
        echo "</div>";
        echo "<form action=\"installdb.php\">
				<input type=\"hidden\" name=\"Proceed\" value=\"true\" />
              <input type=\"submit\" name='page' class=\"btn btn-primary\" value=\"Retry MySQL\" />
              </form>";
        die();
	}
}
?>
<?php if(!($_POST['Proceed']=="true")) { 
	echo "<div class=\"alert alert-info\" role=\"alert\">";
	echo "<form action=\"installdb.php\" method=\"POST\">";
	echo "<p><input type=\"checkbox\" name=\"Proceed\" value=\"true\" /><b>Check this box</b> to indicate that you have entered the information in config.php!</p><p><input type=\"submit\" value=\"Install Database\" />"; 
	echo "</form>";
	echo "</div>";
	} ?>
</div>
</body>
</html>