<?php
# Set these variables:
$mysqlserveraddress = 'localhost';
$mysqlusername = 'starmade';
$databasename = 'starmadedb';
$password = 'thisismymysqlpassword';
$pathtologs = '/home/starmade/StarMade/logs/';

$link = mysql_connect($mysqlserveraddress, $mysqlusername, $password);

if (!$link) {
    die('Could not connect: ' . mysql_error());
}

$db_selected = mysql_select_db($databasename, $link);

if (!$db_selected) {
    die ('Can\'t use starmade database : ' . mysql_error());
}

$command = "grep -i -h \"logged in RegisteredClient\" {$pathtologs}serverlog.txt.* | awk '{print $1 \",\" $7}' | sed 's/\[//' | sort | uniq";
$output = shell_exec($command);

if ($output) {
	$entries = explode("\n", $output);
	foreach($entries as $entry) {
		if ($entry != '') {
			$data = explode(",", $entry);

			unset($sqlquery);
			sqlquery = sprintf("INSERT IGNORE INTO connectionlog (date, playername) VALUES('%s', '%s');", mysql_real_escape_string($data[0]), mysql_real_escape_string($data[1]), mysql_real_escape_string($data[0]), mysql_real_escape_string($data[1]));
			$data = mysql_query($sqlquery) or die(mysql_error());
		}
	}
}
?>