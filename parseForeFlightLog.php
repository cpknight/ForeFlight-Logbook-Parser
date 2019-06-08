#!/usr/bin/env php
<?php // ----------------------------------------------------------------------
//   parseForeFlightLog.php - parses ForeFlight logbook data and spits out
//      MySQL queries. This supports my logbook-output project. Subject to
//      the BSD 3-clause license in LICENSE.
// ---------------------------------------------------------------------------

ini_set('memory_limit','512M');
ini_set("auto_detect_line_endings", true);
date_default_timezone_set ("UTC");

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

if (!isset($argv[1]))
{
  echo "Usage: " . $argv[0] . " [ForeFlight Logbook CSV Export File]\n";
  echo "   eg. " . $argv[0] . " logbook_2019-06-07_23_59_59.csv\n";
  exit(-1);
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

$lines = file($argv[1]);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// First, determine where to look for aircraft and flights data.

$aircraftDataLine = -1;
$flightDataLine = -1;

foreach ($lines as $lineNumber => $line)
{
  if (substr($line, 0, 14) === "Aircraft Table")
    $aircraftDataLine = $lineNumber;

  if (substr($line, 0, 13) === "Flights Table")
    $flightDataLine = $lineNumber;
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Second, we parse the CSV data and put stuff into arrays.

$aircraftCSVData = array();
$flightCSVData = array();

foreach ($lines as $lineNumber => $line)
{
  if (($lineNumber > $aircraftDataLine) and ($lineNumber < ($flightDataLine - 1)))
    array_push($aircraftCSVData,$line);
  else if ($lineNumber > $flightDataLine)
    array_push($flightCSVData,$line);
}

$aircraftData = csvToArray($aircraftCSVData);
$flightData = csvToArray($flightCSVData);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Next, we build a big long SQL string, and dump that to STDOUT.

$sql .= arrayToMySQLCreateTable("Aircraft", $aircraftData);
$sql .= arrayToMySQLInsertInto("Aircraft", $aircraftData);
$sql .= arrayToMySQLCreateTable("Flights", $flightData);
$sql .= arrayToMySQLInsertInto("Flights", $flightData);
print $sql;

// ---------------------------------------------------------------------------
// Function to convert the CSV data into an array.
// ---------------------------------------------------------------------------

function csvToArray($data)
{
  $csv = array_map('str_getcsv', $data);
  array_walk($csv, function(&$a) use ($csv) {
    $a = array_combine($csv[0], $a);
  });
  array_shift($csv); # remove column header
  return $csv;
}

// ---------------------------------------------------------------------------
// Function to convert the array into MySQL INSERT INTO queries.
// ---------------------------------------------------------------------------

function arrayToMySQLInsertInto($tableName, $data)
{
  foreach ($data as $entry)
  {
    $entry = array_filter($entry); // remove empty elements
    $columns = implode("`,`",array_keys($entry));
    $escaped_values = array_map('addslashes', array_values($entry));
    $values  = implode("\", \"", $escaped_values);
    $sql .= "INSERT INTO `$tableName` (`$columns`) VALUES (\"$values\");\n";
  }

  return $sql;
}

// ---------------------------------------------------------------------------
// Function to build a CREATE TABLE MySQL query;
// ---------------------------------------------------------------------------

function arrayToMySQLCreateTable($tableName, $data)
{
  $columns = array_keys($data[1]);

  $sql = "CREATE TABLE `$tableName` ( ";
  foreach ($columns as $column)
    if ($column != "")
      $sql .= "`$column` TEXT,"; // note all are text for this purpose. YMMV.

  $sql = substr($sql, 0, strlen($sql) -1); // remove extra ,
  $sql .= ");\n";

  return $sql;
}

// ---------------------------------------------------------------------------
//                                                                         eof ?>
