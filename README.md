# CallTom
CallTom is a simple webapplication written in PHP to quickly call someone.

## Introduction
How often have you been called for dinner by your parents, but haven't you heard it because you was listening to music? It happened often to me. To prevent this (and because they were getting sick of it), I wrote CallTom. The concept is simple: They have an simple shortcut on their homescreen which they press. With a simple tap, they can call you without screaming on top of their lungs.

## Requirements and stuff
CallTom has a few requirements in order to run smoothly:
- CallTom's notification are being sent using PushBullet. The person you're calling, obviously, needs to have PushBullet. Also, you'll need their PushBullet API key.
- CallTom has been written to work with MySQL.
- CallTom should work fine with the latest PHP.
- CallTom runs fine on Apache, other webservers may also work. Your mileage may vary.

## Limitations
CallTom has no authentication. Making this available to the Internet is not recommended. Also, CallTom only supports calling one person.

## Setting up
Simply put the files in a folder on your webserver and edit the settings.php file. Enter the API key for the person you're calling. Don't forget to change the webroot setting. Create a database and a user.  When you're done, use the following SQL statements to create the tables:
```
CREATE TABLE `users` (
   `userid` varchar(15) NOT NULL COMMENT 'u_$uniqid',
   `username` varchar(30) NOT NULL,
   PRIMARY KEY (userid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `devices` (
   `deviceid` varchar(15) NOT NULL COMMENT 'd_$uniqid',
   `userid` varchar(15) NOT NULL,
   `devicename` varchar(30) NOT NULL,
   PRIMARY KEY (deviceid),
   FOREIGN KEY (userid) REFERENCES users(userid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `calls` (
	`callid` varchar(15) NOT NULL COMMENT 'c_$uniqid',
	`deviceid` varchar(15) NOT NULL,
	`type` int(1) NOT NULL,
	`time` int(11) NOT NULL,
	`response` int(1) NOT NULL,
	`reminded` int(1) NOT NULL,
	`message` text,
	`remsg` text,
	PRIMARY KEY (callid),
	FOREIGN KEY (deviceid) REFERENCES devices(deviceid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```	
When everything is set up, open CallTom with your webbrowser. Follow the directions.

## Thanks to
I want to thank [subtlepatterns.com](www.subtlepatterns.com) for the backgrounds.

## Want to improve CallTom?
If you spot a problem with CallTom, feel free to create an issue or do a pull request! Thanks!
