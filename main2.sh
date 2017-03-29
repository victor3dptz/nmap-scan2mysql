#!/bin/bash

checkip=`mktemp`
#echo "Check IP: "$1

# Пропуск своего IP
bash getmyip.sh > $checkip
while IFS='' read -r line || [[ -n "$line" ]]; do
if [ "$line" == "$1" ]
then
rm -f $checkip
exit
fi
done < "$checkip"

#quick scan
sudo nmap -Pn -T4 --host-timeout 1h --max-retries 3 --defeat-rst-ratelimit -F -sV -O --system-dns $1 > $checkip

php host.php $checkip $1
empty=`cat $checkip|grep "(0 hosts up)"`
if [ -z "$empty" ]
then
mv $checkip ./result/$1.txt
fi
rm -f $checkip
