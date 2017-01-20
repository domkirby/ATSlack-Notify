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
ini_set('display_errors', 1); //Display errors in case something occurs
if(empty($_SERVER['HTTPS'])) die("USE HTTPS DUMMY");
header('Content-Type: application/json'); //Set the header to return JSON, required by Slack
require_once __DIR__ . '/config.php';
$ticketNumber = $_POST['number'];
$ticketId = $_POST['id'];
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/src/autoload.php';
if(empty($_GET['token']) || ($_GET['token'] != $dbmantoken)) die("Slack token invalid."); //If Slack token is not correct, kill the connection. This allows only Slack to access the page for security purposes.
if(empty($_GET['text'])) die("No text provided."); //If there is no text added, kill the connection.
$exploded = explode(" ",$_GET['text']); //Explode the string attached to the slash command for use in variables.

$explodeadmins = explode("|", $adminlist); //Explode list of acceptable admins.
if(!in_array($_GET["user_name"],$explodeadmins))
{
    die("You are not authorized to access this command. Only the following users can: " . implode(", ",$explodeadmins));
}

//Check to see if the first command in the text array is actually help, if so redirect to help webpage detailing slash command use.
if ($exploded[0]=="help") {
    die("The following commands are available:\nlistmap - List all username mappings between Autotask and Slack\naddmap (slackname) (atusername) - Associate the two names\nremovemap (slackname) - Remove a mapping");
}

$mysql = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname); //Connect MySQL

if (!$mysql) //Check for errors
{
    die("Connection Error: " . mysqli_connect_error());
}
//listmap
if ($exploded[0]=="listmap")
{
    $sql = "SELECT * FROM `usermap`"; //SQL Query to select all users

    $result = mysqli_query($mysql, $sql); //Run result
    $output = "List of username mappings:\n";
    if(mysqli_num_rows($result) > 0) //If there were too many rows matching query
    {
        while($row = mysqli_fetch_assoc($result))
        {
            $output = $output . "Slack: " . $row["slackuser"] . " | Autotask: " . $row["atusername"] . "\n";
        }
        die($output);
    }
    else
    {
        die("No user mappings found in database.");
    }
}
//addmap
else if ($exploded[0]=="addmap")
{
    if (!array_key_exists(2,$exploded))
    {
        die("Error: Please ensure you're entering the following: addmap (slack name) (connectwise username)");
    }
    $sql = "INSERT INTO `usermap` (`slackuser`, `atusername`) VALUES ('" . $exploded[1] . "', '" . $exploded[2] . "');"; //SQL Query to insert new map

    if(mysqli_query($mysql,$sql))
    {
        die("Successfully added mapping for Slack User " . $exploded[1] . " to Autotask User " . $exploded[2]);
    }
    else
    {
        die("MySQL Error: " . mysqli_error($mysql));
    }
}
//removemap
else if ($exploded[0]=="removemap")
{
    if (!array_key_exists(1,$exploded))
    {
        die("Error: Please ensure you're entering the following: removemap (slack name)");
    }
    $sql = "DELETE FROM .`usermap` WHERE `usermap`.`slackuser` = '" . $exploded[1] . "';"; //SQL Query to remove map

    if(mysqli_query($mysql,$sql))
    {
        die("Successfully removed mapping for Slack User " . $exploded[1]);
    }
    else
    {
        die("MySQL Error: " . mysqli_error($mysql));
    }
}
?>