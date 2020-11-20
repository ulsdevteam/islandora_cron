#!/bin/bash
#
# Script to clear all drupal caches for SITES. 
# This functionality is already covered by the Drupal cron, but this script is retained as a basis for future drush calls
#
# bdgregg 7/13/2016
#

# Environment
SITES='site.edu'
DRUPAL_DIR=/var/www/html/drupal7
DRUSH=/usr/bin/drush
NOW=`date +%Y%m%d-%H%M%S`
LOG="/opt/islandora_cron/logs/cron-$NOW.log"

echo "Islandora Cron Log: $NOW" > $LOG;

for SITE in `echo $SITES`
do 
   echo "Clearing Cache for: $SITE" >> $LOG
   $DRUSH --root=$DRUPAL_DIR --uri=$SITE cc all >> $LOG 2>&1
   echo "" >>$LOG
done
