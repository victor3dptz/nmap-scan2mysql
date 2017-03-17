#!/bin/bash
checkip=`mktemp`
echo "Check IP: "$1

#full scan
#sudo nmap -Pn -T4 --max-retries 3 --defeat-rst-ratelimit -p- -sSU -sV -O -n $1 > $checkip
#fast scan
sudo nmap -Pn -F -O -n $1 > $checkip

php host.php $checkip
empty=`cat $checkip|grep "(0 hosts up)"`
if [ -z "$empty" ]
then
mv $checkip ./result/$1.txt
fi
rm -f $checkip
