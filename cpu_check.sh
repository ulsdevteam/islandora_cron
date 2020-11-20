#!/bin/bash
# by Paul Colby (http://colby.id.au), no rights reserved ;)

PREV_TOTAL=0
PREV_IDLE=0

TIMES=0

while [ $TIMES -le 5 ]
do
  # Get the total CPU statistics, discarding the 'cpu ' prefix.
  CPU=(`sed -n 's/^cpu\s//p' /proc/stat`)
  IDLE=${CPU[3]} # Just the idle CPU time.

  # Calculate the total CPU time.
  TOTAL=0
  for VALUE in "${CPU[@]}"; do
    let "TOTAL=$TOTAL+$VALUE"
  done
  # Calculate the CPU usage since we last checked.
  let "DIFF_IDLE=$IDLE-$PREV_IDLE"
  let "DIFF_TOTAL=$TOTAL-$PREV_TOTAL"
  let "DIFF_USAGE=(10000*($DIFF_TOTAL-$DIFF_IDLE)/$DIFF_TOTAL+5)"

  # Remember the total and idle CPU times for the next check.
  PREV_TOTAL="$TOTAL"
  PREV_IDLE="$IDLE"

  TIMES=$((TIMES+1))

  if [ $TIMES -eq 6 ]
  then
    # This output value will need to be divided by 100 in order to get the percentage.
    echo "$DIFF_USAGE"
  else
    # Wait before checking again.
    sleep 1
  fi
done

