#!/bin/bash

ipnet=`mktemp`
ipnet2=`mktemp`
mactemp=`mktemp`
# Получение ip для полного скана
mysql -u root -p1234567 -h 192.168.0.113 -N nmap<search.sql > $ipnet
# Пропуск хостов без MAC
while IFS='' read -r line || [[ -n "$line" ]]; do
sudo nmap -sP -n $line > $mactemp
m=`cat $mactemp | grep "MAC Address: "`
if [ ! -z "$m" ]
then
echo $line >> $ipnet2
echo $line" "$m
fi
done < "$ipnet"
rm -f $mactemp

i=0
while IFS='' read -r line || [[ -n "$line" ]]; do
((i++))
# & запуск 20 процессов одновременно
if [ $i -lt 20 ]
then
bash fullsearch2.sh $line &
else
bash fullsearch2.sh $line &
i=0
date
wait
fi
done < "$ipnet2"
# ожидание
wait
rm -f $ipnet
rm -f $ipnet2
