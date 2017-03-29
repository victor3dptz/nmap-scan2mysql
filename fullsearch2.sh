#!/bin/bash
checkip=`mktemp`

# Пропуск своего IP
bash getmyip.sh > $checkip
while IFS='' read -r line || [[ -n "$line" ]]; do
if [ "$line" == "$1" ]
then
rm -f $checkip
exit
fi
done < "$checkip"

echo "Full TCP Scan: "$1
sudo nmap -Pn -T4 --host-timeout 1h --max-retries 3 --defeat-rst-ratelimit -sV -O -p- --system-dns $1 > $checkip

fail=`cat $checkip|grep "Skipping host "`
if [ -n "$fail" ]
then
echo "Quick TCP Scan: "$1
sudo nmap -Pn -T4 --host-timeout 1h --max-retries 3 --defeat-rst-ratelimit -sV -O --system-dns $1 > $checkip
fi

php host.php $checkip

echo "Full UDP Scan: "$1
sudo nmap -Pn -T4 --host-timeout 1h --max-retries 3 -p- -sU --system-dns $1 > $checkip

fail=`cat $checkip|grep "Skipping host "`
if [ -n "$fail" ]
then
echo "Quick UDP Scan: "$1
sudo nmap -Pn -T4 --host-timeout 1h --max-retries 3 -sU --system-dns $1 > $checkip
fi

php host.php $checkip

rm -f $checkip
