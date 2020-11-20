<?php

/**
 * LOOP from 1 to max items
 * 1. read a PID value from the mods_to_label.txt file.
 * 2. is the value in the completed_mods_to_label.txt?
 *    YES = skip, go back to step 1.
 *    NO = proceed to step 3. with this file.
 * 3. Parse the MODS and update the object label if it is different from the value found in MODS.
 * RETURN to loop
 */

set_time_limit(0);

// The file to process
define('PROCESS_FILE', 'mods_to_label_part3.txt');

// XML Transformation

// If this variable is set to any PID that is in the spreadsheet, all items above it will not be processed -- making it
// the first PID to be processed;
$first_pid = NULL;

// Set this variable if we only need to process a specific number of items from the spreadsheet.
$process_exactly_howmany = 2000;
// $process_exactly_howmany = PHP_INT_MAX;

// Load our own Library.
require_once(dirname(__FILE__) .'/uls-tuque-lib.php');

// Setup Tuque
$path_to_tuque = get_config_value('tuque','path_to_tuque');
if (file_exists($path_to_tuque)) {
        require_once($path_to_tuque . 'Cache.php');
        require_once($path_to_tuque . 'FedoraApi.php');
        require_once($path_to_tuque . 'FedoraApiSerializer.php');
        require_once($path_to_tuque . 'Object.php');
        require_once($path_to_tuque . 'Repository.php');
        require_once($path_to_tuque . 'RepositoryConnection.php');
} else {
        print "Error - Invalid path to Tuque.\n";
        exit(1);
}
_log('started ' . date('H:i:s'));

$connection = getRepositoryConnection();
$repository = getRepository($connection);

$filename = dirname(__FILE__) . '/' . PROCESS_FILE;
$done_filename = $filename . '.done';
$file = file($filename);
$done_files = explode("\n", file_get_contents($done_filename));

$s = "";
$i = 0;
$process = (is_null($first_pid) ? TRUE : FALSE);

foreach ($file as $pid) {
  $pid = trim($pid);
echo $pid . "\n\n";
  // To set the start of processing at a specific PID value
  if (!$process && !is_null($first_pid)) {
    $process = $pid == $first_pid;
  }
  if ($process && $i < $process_exactly_howmany) {
    $done = (!(array_search($pid, $done_files) === FALSE));
    if ($done) {
      _log("skipping $pid - done already");
    }
    else {
      $i++;
      $s .= 'row#' . $i . ' = ' . $pid . ' / ' . (($process_exactly_howmany == PHP_INT_MAX) ? 'all' : $process_exactly_howmany) . "\n";

      $object = $repository->getObject($pid);

      if (is_object($object)) {
        $s .= 'object(' . $pid . ') ' . (is_object($object) ? ' loaded ok' : ' NOT LOADED');
        $s .= ', ' . (is_object($object) && isset($object['MODS']) ? 'has MODS' : 'no MODS') . "\n";
        $s .= process_changes($object, $repository);
        file_put_contents($done_filename, $pid . "\n", FILE_APPEND);
      }
    }
  }
}

_log('done ' . date('H:i:s'));
// $s was displayed as full HTML when we'd die($s), but the devel module does not print any html tags.


/**
 * This will transform the MODS according to the transform file TRANSFORM_STYLESHEET.
 */
function process_changes($object, $repository) {
  global $max_geo_idx;

  $mods = $object['MODS'];
  $tempFilename = tempnam("/tmp", "MODS_xml_initial_");
  // save the html body to a file -- datastream can load from the file
  $mods_file = $mods->getContent($tempFilename);
  if ($mods_file) {
    $mods_file = implode("", file($tempFilename));
    doLabel($object, $mods_file);
    unlink($tempFilename);
    _log('PROCESSING DONE for ' . $object->id);
  }
}

/**
 * This will update the object label based on the MODS titleInfo title value.
 */
function doLabel($object, $mods_content) {
  echo $mods_content . "\n-------------------------------\n\n";

  $mods_xml = new SimpleXMLElement($mods_content);
  $mods_xml->registerXPathNamespace('mods', 'http://www.loc.gov/mods/v3');

  $title_results = $mods_xml->xpath('//mods:mods[1]/mods:titleInfo/mods:title');
  if (is_array($title_results)) {
    $titles = $title_results[0];
    if (is_object($titles)) {
      $titles_arr = (array) $titles;
      if (is_array($titles_arr)) {
        $title = trim($titles_arr[0]);
        echo $title . "\n";
        if ($object->label <> $title) {
          $object->label = $title;
        }
      }
    }
  }
  return;
}

function _log($message, $logfile = '') {
  if (!$logfile) {
    $logfile = dirname(__FILE__) . '/logfile';
  }
  if ($logfile) {
    error_log(date('c') . ' ' . $message."\n", 3, $logfile);
  }
  else {
    error_log($message);
  }
}

