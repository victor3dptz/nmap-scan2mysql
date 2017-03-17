#!/bin/bash

ipnet=`mktemp`
nmap -sL -n $1 | grep 'Nmap scan report for' | cut -f 5 -d ' ' >> $ipnet

while IFS='' read -r line || [[ -n "$line" ]]; do
# & запуск несколько процессов
bash main2.sh $line &
done < "$ipnet"
# ожидание
wait
rm -f $ipnet
