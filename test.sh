#!/bin/bash

checkip=`mktemp`

#sudo nmap -Pn -O -A 192.168.0.10 > .test.tmp
#sudo nmap -sL -n 192.168.0.186 > .test.tmp
#sudo nmap -Pn -A 10.4.8.38 -sSU -n > .test.tmp
#sudo nmap -Pn -F -O 192.168.0.186 > $checkip

php host.php tmp

rm -f $checkip
