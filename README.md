# StarMadeJoinStats
StarMadeJoinStats is a small project to provide a few simple web graphs on player join statistics for the game StarMade

## About ##

StarMadeJoinStats is a small project utilising MySQL, PHP and javascript to provide a few simple graphs on player join statistics for the game StarMade.

It works by parsing the game log files on a scheduled basis (crontabs), and inserting into a MySQL table unique appearances of a player per day. Armed with that raw data, some simple analysis can be done to produce useful information such as new vs returning players per day and per month.

An example of this can be seen at: https://www.sheen.id.au/starmadejoinstats/

## Prerequisites ##

MySQL is required - in debian this is simple to install:
apt-get install mysql-server

## Installation ##
Create the MySQL database and user, and set permissions
mysql -u root -p
mysql> create user 'starmade'@'localhost' identified by 'thisismymysqlpassword';
mysql> create database starmadedb;
mysql> grant usage on *.* to starmade@localhost identified by 'thisismymysqlpassword';
mysql> grant all privileges on starmadedb.* to starmade@localhost;

Create the table
CREATE TABLE IF NOT EXISTS `connectionlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playername` varchar(50) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `playername_date` (`playername`,`date`),
  KEY `playername` (`playername`,`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

## Configuration ##
### starmadeconnectionlog.php ###
place this file in your starmade server folder (the same folder as the game)
Edit the starmadeconnectionlog.php and set the variables at the top to be the appropriate values
### starmadeupdatedailyplayercount.sh ###
place this file in your starmade server folder (the same folder as the game)
Edit the starmadeupdatedailyplayercount.sh and set the variables at the top to be the appropriate values
Set the permission for this file to allow execute: chmod +x starmadeupdatedailyplayercount.sh
### www files ###
place these in a folder under your www root - be sure to include the js folder.
You will probably need to give permissions to the webserver on the js files
chgrp -R www-data js/*.*

### crontabs ###
#crontab one minute past every hour to create log entries in the mysql connectionlog table
1 * * * * /usr/bin/php -f /home/starmade/StarMade/starmadeconnectionlog.php
#crontab on the second minute of every hour to generate the text files we graph on
2 * * * * /home/starmade/StarMade/starmadeupdatedailyplayercount.sh