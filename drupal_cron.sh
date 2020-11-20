#!/bin/bash
#
# Script run Drupal cron for each Islandora site
#

# Environment
CRONS='http://site.org/cron.php?cron_key='
for CRON in $CRONS
do 
   wget -O - -q -t 1 $CRON
done
