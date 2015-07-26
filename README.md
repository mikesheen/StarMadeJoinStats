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

```
mysql -u root -p
mysql> create user 'starmade'@'localhost' identified by 'thisismymysqlpassword';
mysql> create database starmadedb;
mysql> grant usage on *.* to starmade@localhost identified by 'thisismymysqlpassword';
mysql> grant all privileges on starmadedb.* to starmade@localhost;
```

Now create the table

```
CREATE TABLE IF NOT EXISTS `connectionlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playername` varchar(50) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `playername_date` (`playername`,`date`),
  KEY `playername` (`playername`,`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
```

## Configuration ##
### starmadeconnectionlog.php ###
This PHP script parses ALL the log files in your server log folder and puts the entries into the MySQL table.
place this file in your starmade server folder (the same folder as the game)
Edit the starmadeconnectionlog.php and set the variables at the top to be the appropriate values
### starmadeupdatedailyplayercount.sh ###
This shell script runs queries against the MySQL table to produce the text files (CSV) the web page needs for the graphs. I opted to generate the CSV's once an hour because I didn't want the web clients causing database hits - so this is a poor mans caching strategy.
place this file in your starmade server folder (the same folder as the game)
Edit the starmadeupdatedailyplayercount.sh and set the variables at the top to be the appropriate values
Set the permission for this file to allow execute: 

```
chmod +x starmadeupdatedailyplayercount.sh
```
### www files ###
place these in a folder under your www root - be sure to include the js folder.
You will probably need to give permissions to the webserver on the js files

```
chgrp -R www-data js/*.*
```
### crontabs ###
You need to add some crontabs to regularly parse the logs and generate the files the graphs report on. I opted for once an hour. The unique index on the table means you can parse the same log file without risk of duplicates - this was the only reliable way I found of updating the logs with the retention policy which StarMade has for log files. You don't want the crontab to be more than a day, and and it only takes a fraction of a second to run - one hour has worked fine in my production server for over a year, with no impact on server peformance.
Edit the crontab with:

```
crontab -e
```
Then add the crontab entries:

```
# m h  dom mon dow   command
#crontab one minute past every hour to create log entries in the mysql connectionlog table
1 * * * * /usr/bin/php -f /home/starmade/StarMade/starmadeconnectionlog.php
#crontab on the second minute of every hour to generate the text files we graph on
2 * * * * /home/starmade/StarMade/starmadeupdatedailyplayercount.sh
```

It will be some time (days) before you start to get a useful amount of data. If you have older log files, you can feed that into the MySQL table by copying the starmadeconnectionlog.php, and editing the log file source in that, then running it for a one-off import of older logs.

eg: copy starmadeconnectionlog.php to legacyimport.php
Then edit legacyimport.php to point to your older log files
Now run the php manually:

```
/usr/bin/php -f /home/starmade/StarMade/legacyimport.php
```