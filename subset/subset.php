<?php

// Create a TSV file for a subset of the data

$basedir = dirname(__FILE__) . '/data/powoNames';

$filename = $basedir . '/taxon.txt';

$row_count = 0;

// what items do we want?
$filter = array(
'urn:lsid:ipni.org:names:251403-2',
'urn:lsid:ipni.org:names:77150994-1',
'urn:lsid:ipni.org:names:17173610-1',
'urn:lsid:ipni.org:names:17096480-1',
'urn:lsid:ipni.org:names:17192290-1',
'urn:lsid:ipni.org:names:207119-2',
);

$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$line = fgets($file_handle);
		
	$row = explode("\t",trim($line));
	
	$go = false;
	
	$go = in_array($row[0], $filter);
	
	if ($go)
	{
		echo $line;
	}
	
	$row_count++;	
	
}

?>
