#! /bin/bash
for i in `cat ip.txt`
do
        ping -c2 -W1 ${i} &> /dev/null
        if [ "$?" == "0" ]; then
                echo "$i is UP"
        else
                echo "$i is DOWN"
        fi
done
