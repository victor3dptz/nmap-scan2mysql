#!/bin/bash


while IFS='' read -r line || [[ -n "$line" ]]; do
bash main.sh $line
done < "target.txt"

