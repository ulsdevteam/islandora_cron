<?php

/**
 * LOOP from 1 to max items
 * 1. read a PID value from the mods_to_dc.txt file.
 * 2. is the value in the completed_mods_to_dc.txt?
 *    YES = skip, go back to step 1.
 *    NO = proceed to step 3. with this file.
 * 3. Perform pitt_mods_to_dc.xslt on it and save updated DC and add this PID to completed_mods_to_dc.txt.
 * RETURN to loop
 */

set_time_limit(0);

// The file to process
define('PROCESS_FILE', 'mods_to_dc_part3.txt');

// XML Transformation
define('TRANSFORM_MODS2DC_STYLESHEET', dirname(__FILE__).'/xsl/upitt_mods_to_dc.xsl');
define('TRANSFORM_FINDAID_COLLECTION_MODS2DC_STYLESHEET', dirname(__FILE__).'/xsl/upitt_findAid_Collection_mods_to_dc.xsl');

// If this variable is set to any PID that is in the spreadsheet, all items above it will not be processed -- making it
// the first PID to be processed;
$first_pid = NULL;

// Set this variable if we only need to process a specific number of items from the spreadsheet.
$process_exactly_howmany = 3000;
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

      try {
        $object = $repository->getObject($pid);
        if (is_object($object)) {
          $s .= 'object(' . $pid . ') ' . (is_object($object) ? ' loaded ok' : ' NOT LOADED');
          $s .= ', ' . (is_object($object) && isset($object['MODS']) ? 'has MODS' : 'no MODS') . "\n";
          $s .= process_changes($object, $repository);
        }
        else {
          file_put_contents(dirname(__FILE__) . '/problem_pids.txt', $pid . "\n", FILE_APPEND);
        }
      }
      catch (Exception $e) {
        file_put_contents($done_filename, $pid . "\n", FILE_APPEND);
        file_put_contents(dirname(__FILE__) . '/problem_pids.txt', $pid . "\n", FILE_APPEND);
      }
      file_put_contents($done_filename, $pid . "\n", FILE_APPEND);
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

  if (isset($object['MODS'])) {
    $mods = $object['MODS'];
    $tempFilename = tempnam("/tmp", "MODS_xml_initial_");
    // save the html body to a file -- datastream can load from the file
    $mods_file = $mods->getContent($tempFilename);
    if ($mods_file) {
      $mods_file = implode("", file($tempFilename));
      // This will update the DC record by transforming the current MODS xml.
      doDC($object, $mods_file, $repository);
      unlink($tempFilename);
      _log('PROCESSING DONE for ' . $object->id);
    }
  }
}

/**
 * Helper function to transform the MODS to get dc.
 */
function doDC($object, $mods_content, $repository) {
  $dsid = 'DC';
  $dc_datastream = isset($object[$dsid]) ? $object[$dsid] : $object->constructDatastream($dsid);

  // Get the DC by transforming from MODS.
  // For Collection level objects, the transform needs to be different
  $models = $object->models;

  $transform = ((array_search('islandora:findingAidCModel', $models) === FALSE) && (array_search('islandora:collectionCModel', $models) === FALSE)) ? TRANSFORM_MODS2DC_STYLESHEET : TRANSFORM_FINDAID_COLLECTION_MODS2DC_STYLESHEET;
  if ($mods_content) {
    $new_dc = _runXslTransform(
            array(
              'xsl' => $transform,
              'input' => $mods_content,
              'pid' => $object->id,
            )
          );
  }

  if (isset($new_dc)) {
    $dc_datastream->setContentFromString($new_dc);
  }
  $object->ingestDatastream($dc_datastream);
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

/**
  * Run an XSLT, and return the results.
  *
  * @param array $info
  *   An associative array of parameters, containing:
  *   - input: The input XML in a string.
  *   - xsl: The path to an XSLT file.
  *   - php_functions: Either a string containing one or an array containing
  *     any number of functions to register with the XSLT processor.
  *
  * @return string
  *   The transformed XML, as a string.
  */
function _runXslTransform($info) {
  $xsl = new DOMDocument();
  $xsl->load($info['xsl']);
  $input = new DOMDocument();
  $input->loadXML($info['input']);

  $processor = new XSLTProcessor();
  $processor->importStylesheet($xsl);
  $processor->setParameter('', 'pid', $info['pid']);

  return $processor->transformToXML($input);
}

