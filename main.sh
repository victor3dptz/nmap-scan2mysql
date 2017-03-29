#!/bin/bash

ipnet=`mktemp`
echo "Start scan: "$1
nmap -sL -n $1 | grep 'Nmap scan report for' | cut -f 5 -d ' ' >> $ipnet
i=0
while IFS='' read -r line || [[ -n "$line" ]]; do
((i++))
# & запуск 20 процессов одновременно
if [ $i -lt 20 ]
then
bash main2.sh $line &
#echo $i
else
i=0
date
wait
fi
done < "$ipnet"
# ожидание
wait
rm -f $ipnet
