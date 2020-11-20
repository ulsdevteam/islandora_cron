<?php

define('TIME_THRESHOLD', 600);

/**
 * If an object needs to process OCR without being killed, it should be queued up with a
 * slightly different job name.  This script will only kill the OCR task if the job name is the
 * default of "upitt_workflow_generate_ocr_datastream".  The job that will not be killed is
 * named "upitt_workflow_generate_notimelimit_ocr_datastreams".
 */

$database = get_config_value('mysql', 'database');
$dbuser = get_config_value('mysql', 'username');
$dbpassword = get_config_value('mysql', 'password');
$dbhost = get_config_value('mysql', 'host');
$server_name = get_config_value('server', 'name');

// Create connection
$conn = new mysqli($dbhost, $dbuser, $dbpassword);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
/* change db to world db */
$conn->select_db("gearman_prod");
/* return name of current default database */
if ($result = $conn->query("SELECT DATABASE()")) {
    $row = $result->fetch_row();
    $result->close();
}

kill_tesseract_jobs_after_time()
kill_convert_jobs_after_time()

  function kill_tesseract_jobs_after_time(){
    # reworked this block for convert tasks
    $command = 'ps -C tesseract -F';
    $output = $ret = array();
    $call = exec($command, $output, $ret);

    $keys = array();
    foreach ($output as $i => $process_line) {
      $process_line = str_replace("  ", " ", $process_line);
      $process_line = str_replace("  ", " ", $process_line);
      $process_line = str_replace("  ", " ", $process_line);
      $process_line = str_replace("  ", " ", $process_line);
      $process_line = str_replace("  ", " ", $process_line);
      $output[$i] = $process_line;
      $parts = explode(" ", $process_line);
      if (count($keys) < 1) {
        $keys = explode(" ", $process_line);
      }
    }

    $pid_index = array_search('PID', $keys);
    $time_index = array_search('TIME', $keys);

    $first_line = TRUE;
    $run_output = array();
    foreach ($output as $i => $process_line) {
      if ($first_line) {
        $first_line = FALSE;
      }
      else {
        $parts = explode(" ", $process_line);

        $this_pid = $parts[$pid_index];
        $this_time = $parts[$time_index];
        @list($h, $m, $s) = explode(":", $this_time);
        $time_in_seconds = ($h * 3600) + ($m * 60) + $s;

        $tesseract_index= array_search('/usr/local/bin/tesseract', $parts);
        $this_payload = $parts[($tesseract_index + 1)];
        $this_payload = str_replace(
          array('pitt_', '_OBJ.tif', '/tmp/'),
          array('pitt:', '', ''),
          $this_payload);

        // Check to see if this line exceeds the threshold.
        if ($time_in_seconds > TIME_THRESHOLD) {
          $markup = kill_pid_and_remove_ocr_job($this_pid, $this_payload, $conn, $time_in_seconds);
          if ($markup) {
            $run_output[] = $markup;
          }
        }
      }
    }
    @mysqli_close($conn);

    if (count($run_output) > 0) {
      echo "The CRON job on " . $server_name . " /opt/islandora_cron/kill_pid_after_time.php is killing some OCR jobs because they exceeded the threshold of " . TIME_THRESHOLD . " seconds.\n\n";
      foreach ($run_output as $pid_output) {
        echo $pid_output;
      }
      exit(1);
    }
    else {
      exit(0);
    }
  }

  function kill_convert_jobs_after_time(){
    # reworked this block for convert tasks
    $command = 'ps -C convert -F';
    $output = $ret = array();
    $call = exec($command, $output, $ret);

    $keys = array();
    foreach ($output as $i => $process_line) {
      $process_line = str_replace("  ", " ", $process_line);
      $process_line = str_replace("  ", " ", $process_line);
      $process_line = str_replace("  ", " ", $process_line);
      $process_line = str_replace("  ", " ", $process_line);
      $process_line = str_replace("  ", " ", $process_line);
      $output[$i] = $process_line;
      $parts = explode(" ", $process_line);
      if (count($keys) < 1) {
        $keys = explode(" ", $process_line);
      }
    }

    $pid_index = array_search('PID', $keys);
    $time_index = array_search('TIME', $keys);

    $first_line = TRUE;
    $run_output = array();
    foreach ($output as $i => $process_line) {
      if ($first_line) {
        $first_line = FALSE;
      }
      else {
        $parts = explode(" ", $process_line);

        $this_pid = $parts[$pid_index];
        $this_time = $parts[$time_index];
        @list($h, $m, $s) = explode(":", $this_time);
        $time_in_seconds = ($h * 3600) + ($m * 60) + $s;

        $convert_index= array_search('/usr/bin/convert', $parts);
        $this_payload = $parts[($convert_index + 1)];
        $this_payload = str_replace(
          array('pitt_', '_OBJ.tif', '/tmp/'),
          array('pitt:', '', ''),
          $this_payload);

        // Check to see if this line exceeds the threshold.
        if ($time_in_seconds > TIME_THRESHOLD) {
          $markup = kill_pid_and_remove_derivative_job($this_pid, $this_payload, $conn, $time_in_seconds);
          if ($markup) {
            $run_output[] = $markup;
          }
        }
      }
    }
    @mysqli_close($conn);

    if (count($run_output) > 0) {
      echo "The CRON job on " . $server_name . " /opt/islandora_cron/kill_pid_after_time.php is killing some convert jobs because they exceeded the threshold of " . TIME_THRESHOLD . " seconds.\n\n";
      foreach ($run_output as $pid_output) {
        echo $pid_output;
      }
      exit(1);
    }
    else {
      exit(0);
    }
  }


function kill_pid_and_remove_ocr_job($this_pid, $this_payload, $conn, $time_in_seconds) {
  $ret_markup = '';

  // regardless of whether or not the queue record was found, kill this pid.
  $ret_markup .= "kill -9 $this_pid\n-------------------------------------------------------\n\n";
  exec("kill -9 $this_pid");

  // Take the payload value and split the sequence number from it.
  @list($parent, $sequence) = explode("-", $this_payload);

  $ret_markup .= "Killing tesseract job that was running for " . $time_in_seconds . " seconds: \n  " .
    "pid = " . $this_pid. ", parent = " . $parent . ", payload = " . $this_payload . "\n\n";

  $sql = "SELECT * FROM gearman_queue where data like '%" . $parent . "\"%' AND function_name = 'upitt_workflow_generate_ocr_datastreams'";
  $ret_markup .= "\n" . $sql . "\n\n";
  $queue_unique_keys = array();
  if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_row()) {
      if ($row[0] <> '') {
        $queue_unique_keys[] = $row[0];
      }
    }
    $result->close();
  }
  foreach ($queue_unique_keys as $unique_key) {
    $sql_delete = "DELETE FROM gearman_queue WHERE unique_key = '" . $unique_key . "'";
    $ret_markup .= "DELETE gearman_queue record: \n  " . $sql_delete . "\n";
    $conn->query($sql_delete);
  }
  if (count($queue_unique_keys) < 1) {
    $ret_markup .= "no gearman_queue record found matching this object's parent " . $parent . "\n\n";
  }

  return $ret_markup;
}

function kill_pid_and_remove_derivative_job($this_pid, $this_payload, $conn, $time_in_seconds) {
  $ret_markup = '';

  // regardless of whether or not the queue record was found, kill this pid.
  $ret_markup .= "kill -9 $this_pid\n-------------------------------------------------------\n\n";
  exec("kill -9 $this_pid");

  // Take the payload value and split the sequence number from it.
  @list($parent, $sequence) = explode("-", $this_payload);

  $ret_markup .= "Killing convert job that was running for " . $time_in_seconds . " seconds: \n  " .
    "pid = " . $this_pid. ", parent = " . $parent . ", payload = " . $this_payload . "\n\n";

  $sql = "SELECT * FROM gearman_queue where data like '%" . $parent . "\"%' AND function_name = 'upitt_workflow_generate_ocr_datastreams'";
  $ret_markup .= "\n" . $sql . "\n\n";
  $queue_unique_keys = array();
  if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_row()) {
      if ($row[0] <> '') {
        $queue_unique_keys[] = $row[0];
      }
    }
    $result->close();
  }
  foreach ($queue_unique_keys as $unique_key) {
    $sql_delete = "DELETE FROM gearman_queue WHERE unique_key = '" . $unique_key . "'";
    $ret_markup .= "DELETE gearman_queue record: \n  " . $sql_delete . "\n";
    $conn->query($sql_delete);
  }
  if (count($queue_unique_keys) < 1) {
    $ret_markup .= "no gearman_queue record found matching this object's parent " . $parent . "\n\n";
  }

  return $ret_markup;
}

function get_config_value($section,$key) {
  if ( file_exists('/opt/islandora_cron/kill_pid_after_time.ini') ) {
    $ini_array = parse_ini_file('/opt/islandora_cron/kill_pid_after_time.ini', true);
    if (isset($ini_array[$section][$key])) {
      $value = $ini_array[$section][$key];
      return ($value);
    } else {
      return ("");
    }
  } else {
    return(0);
  }
}
