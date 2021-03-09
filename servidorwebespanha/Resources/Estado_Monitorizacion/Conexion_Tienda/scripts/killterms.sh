#!/bin/bash
sbd=`pidof shellinaboxd | awk '{print $1}'`
ps --ppid $sbd -o pid,pgid | grep -v PID | while read p pgid; do
        if readlink /proc/$p/fd/0 | grep -q deleted; then
                kill -9 -$pgid 2>/dev/null
        fi
done
