# nmap-scan2mysql
Batch scan networks with nmap and store results in MySQL

Preparation:
1) Import base.sql to MySQL
2) Edit host.php - mysql user,password,database,dbhost
3) Put IP hosts and subnets to be scanned to target.txt
4) Edit fullsearch.sh - mysql user,password,database,dbhost

Using:
1) ./run.sh - start quick scan of hosts in target.txt. 
2) ./fullsearch.sh - start full scan (TCP + UDP all ports) of hosts that were previously scanned today with run.sh command (based on scan_log database).

P.S. Work in progress
