# ATSlack-Notify
ATSlack Ticket Notifications (Sends New Ticket Notifications to Slack).
I have limited knowledge of writing these things, this is just about as far as I can take it. Hopefully someone else can add fancy stuff like slash commands! I am working on a few improvements, including a /ataccept command to accept the ticket. However I also have to work so I don't know when that will be ready :).
##Requirements
You'll need a server (Linux is ideal) with PHP and PHP-SOAP client. Many shared web hosts offer this. When I was building and testing it, I was doing so on PHP7 on a Plesk / CentOS web server. *SSL is also required. The task will FAIL if you do not place this on an SSL host* Here is an example of that the notification looks like: https://i.imgur.com/9rLqz0N.png

##Installation
Installation is simple. Download as a ZIP and extract on your web server. Set permissions for config.php to 0600. Edit config.php and fill in the appropriate variables. Your "Autotask Realm" is the first part of the URL after you login (examples: ww5, ww14). Your webservices URL is based on the Realm. If you're in ww5, than your services host should be webservices5.

Your Slack URL is the URL of your Slack Webhook (https://kirbside.slack.com/apps/A0F7XDUAZ-incoming-webhooks). Create your webhook there. You will also want to set the name and icon as we do not do that in ATSlack-Notify. You also need to set the channel within config.php that you want the messages posted to. It does not matter what you picked on Slack.

Username and password should be API credentials for your Autotask instance. You can hit up your account rep to get these for free.

Once you have setup the system on your server, navigate to https://yourserver/folder-where-atslack-is/form.html. Type in an existing ticket number and ID. The ticket ID can be retrieved by opening the ticket in Autotask and copying it from the URL after ticketID=.

You can check the box to get output in the browser (to verify that it is pulling data). You can then repeat the process to test it and push the message to Slack. If all is working, the next steps are in Autotask.

First login and go to Admin>Extensions and Integrations>Other Extensions and Tools>Extension Callouts
Create a **NEW** extension callout with the following variables:
-Memorable Name
-URL: https://yourserver/folder-where-atslack-is/ticketSlack2.php
-Leave Username, Password, and UDF blank
-Transport Method POST
-Data Format Name Value Pair
Save & Close

Now create a workflow rule to fire this callout. For my purposes, I set my workflow to fire when a new ticket is created by an external contact, and filtered it to certain queues that matter most to me. You can design yours however you want. Just make sure that you select your callout under actions.

That's it, you should be good to go :). ENJOY!!
