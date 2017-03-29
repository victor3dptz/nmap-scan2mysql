#!/bin/bash

ifconfig $1 | grep "inet addr" | gawk -F: '{print $2}' | gawk '{print $1}'
