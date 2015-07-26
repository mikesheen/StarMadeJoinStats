mysqluser='starmade';
mysqlpassword='thisismymysqlpassword';
databasename='starmadedb';
pathtooutput='/var/www/starmadejoinstats/';

#counts players per day
mysql --batch --raw --skip-column-names -u$mysqluser -p$mysqlpassword -D$databasename -e "SELECT CONCAT(DATE_FORMAT(date, '%Y-%m-%d'), ',', CAST(count(DISTINCT playername) AS CHAR)) FROM connectionlog WHERE date >= date_sub(now(), interval 12 month) group by date" > ${pathtooutput}starmadedailyplayercount.txt

#counts new (never seen before) players per day
mysql --batch --raw --skip-column-names -u$mysqluser -p$mysqlpassword -D$databasename -e "SELECT CONCAT(DATE_FORMAT(a.date, '%Y-%m-%d'), ',', CAST(count(DISTINCT a.playername) AS CHAR)) FROM (SELECT min(b.date) date, b.playername playername FROM connectionlog b WHERE b.date >= date_sub(now(), interval 12 month) GROUP BY b.playername) a GROUP BY a.date ORDER BY a.date" > ${pathtooutput}starmadedailynewplayers.txt

#counts players per month
mysql --batch --raw --skip-column-names -u$mysqluser -p$mysqlpassword -D$databasename -e "SELECT CONCAT(DATE_FORMAT(date, '%Y-%m-01'), ',', CAST(count(DISTINCT playername) AS CHAR)) FROM connectionlog WHERE date >= date_sub(now(), interval 12 month) GROUP BY YEAR(date), MONTH(date)" > ${pathtooutput}starmademonthlyuniqueplayers.txt

#counts new (never seen before) players per month
mysql --batch --raw --skip-column-names -u$mysqluser -p$mysqlpassword -D$databasename -e "SELECT CONCAT(DATE_FORMAT(a.date, '%Y-%m-01'), ',', CAST(count(DISTINCT a.playername) AS CHAR)) FROM (SELECT min(b.date) date, b.playername playername FROM connectionlog b WHERE b.date >= date_sub(now(), interval 12 month) GROUP BY b.playername) a GROUP BY YEAR(a.date), MONTH(a.date) ORDER BY a.date" > ${pathtooutput}starmademonthlynewplayers.txt

#counts cumulative new players
mysql --batch --raw --skip-column-names -u$mysqluser -p$mysqlpassword -D$databasename -e "SELECT CONCAT(DATE_FORMAT(cumulativetotals.e_date, '%Y-%m-%d'), ',', CAST(cumulativetotals.total_interactions_per_day AS CHAR)) FROM (SELECT e.date AS e_date, count(DISTINCT e.playername) AS num_daily_interactions, (SELECT COUNT(DISTINCT playername) FROM connectionlog WHERE DATE(Date) <= e_date) as total_interactions_per_day FROM connectionlog AS e GROUP BY e_date) AS cumulativetotals;" > ${pathtooutput}starmadecumulativeplayers.txt