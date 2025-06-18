<?php

// Parse wcvp_dwca for location data

require_once(dirname(__FILE__) . '/vendor/autoload.php');

use ML\JsonLD\JsonLD;
use ML\JsonLD\NQuads;

//----------------------------------------------------------------------------------------
// From easyrdf/lib/parser/ntriples
function unescapeString($str)
    {
        if (strpos($str, '\\') === false) {
            return $str;
        }

        $mappings = array(
            't' => chr(0x09),
            'b' => chr(0x08),
            'n' => chr(0x0A),
            'r' => chr(0x0D),
            'f' => chr(0x0C),
           // '\"' => chr(0x22),
            '\'' => chr(0x27)
        );
        foreach ($mappings as $in => $out) {
            $str = preg_replace('/\x5c([' . $in . '])/', $out, $str);
        }

        if (stripos($str, '\u') === false) {
            return $str;
        }

        while (preg_match('/\\\(U)([0-9A-F]{8})/', $str, $matches) ||
               preg_match('/\\\(u)([0-9A-F]{4})/', $str, $matches)) {
            $no = hexdec($matches[2]);
            if ($no < 128) {                // 0x80
                $char = chr($no);
            } elseif ($no < 2048) {         // 0x800
                $char = chr(($no >> 6) + 192) .
                        chr(($no & 63) + 128);
            } elseif ($no < 65536) {        // 0x10000
                $char = chr(($no >> 12) + 224) .
                        chr((($no >> 6) & 63) + 128) .
                        chr(($no & 63) + 128);
            } elseif ($no < 2097152) {      // 0x200000
                $char = chr(($no >> 18) + 240) .
                        chr((($no >> 12) & 63) + 128) .
                        chr((($no >> 6) & 63) + 128) .
                        chr(($no & 63) + 128);
            } else {
                # FIXME: throw an exception instead?
                $char = '';
            }
            $str = str_replace('\\' . $matches[1] . $matches[2], $char, $str);
        }
        return $str;
    }

//----------------------------------------------------------------------------------------

function data_to_schema($obj, $graph, $url, $accepted = true)
{
	// Construct a graph of the results	
	// Note that we use the URL of the object as the name for the graph. We don't use this 
	// as we are outputting triples, but it enables us to generate fake bnode URIs.	
	$graph = new \EasyRdf\Graph($url);	

	$taxon = $graph->resource($url, 'schema:Taxon');
	
	// name
	$name_url = $obj->taxonID;	
	$name = $graph->resource($name_url, 'schema:TaxonName');
	
	if ($accepted)
	{
		$taxon->addResource('schema:additionalType', "http://rs.tdwg.org/dwc/terms/Taxon");
		
		// name is scientificname
		$taxon->addResource('schema:scientificName', $name);
		
		// taxon will have a parent (ubless it is the root of all life)

		if (isset($obj->parentNameUsageID))
		{
			$taxon->addResource('schema:parentTaxon', 'https://powo.science.kew.org/taxon/' . $obj->parentNameUsageID);	
		}	
	}
	else
	{
		// name is an alternative name
		$taxon->addResource('schema:alternateScientificName', $name);		
	}

	// taxon name (which will include authorship)-----------------------------------------
	$namestring = '';		
	if (isset($obj->scientificName))
	{
		$name->add("schema:name", $obj->scientificName);
		$namestring = $obj->scientificName;
	}
	if (isset($obj->scientificNameAuthorship))
	{
		$name->add("schema:author", $obj->scientificNameAuthorship);
		$namestring .= ' ' . $obj->scientificNameAuthorship;
	}		
	
	if ($accepted)
	{			
		$taxon->add('schema:name', $namestring);
	}
	else
	{
		$taxon->add('schema:alternateName', $namestring);	
	}
	
	if (isset($obj->taxonRank))
	{
		$rank = mb_convert_case($obj->taxonRank, MB_CASE_LOWER);
		$name->add("schema:taxonRank", $rank);
	}
	
	
	// reference----------------------------------------------------------
	if (isset($obj->namePublishedIn))
	{
		// reference is for the name
		
		$reference_url = $name_url . '#' . md5($obj->namePublishedIn);
		
		$work = $graph->resource($reference_url, 'schema:CreativeWork');
		$work->add("schema:name", $obj->namePublishedIn);
		$name->add("schema:isBasedOn", $work);
	}
	
	// identifier(s)------------------------------------------------------
	
	if (isset($obj->source) && $accepted)
	{
		// Each name may have a WCSP/WCVP, but we treat these as taxon identifiers because we want to link to distributions
		
		if (preg_match('/http:\/\/apps.kew.org\/wcsp\/namedetail.do\?name_id=(?<id>\d+)/', $obj->source, $m))
		{
			$taxon->addResource('schema:sameAs', $obj->source);
		
			$identifier = $graph->resource($url . '#wcsp', 'schema:PropertyValue');		
			$identifier->add("schema:name", "WCSP")	;
			$identifier->add("schema:value", $m['id']);			
			$taxon->addResource('schema:identifier', $identifier);	
		}
	}
	


	return $graph;
}



//----------------------------------------------------------------------------------------




$filename = dirname(__FILE__) . '/data/wcvp_dwca/wcvp_distribution.csv';

$headings = array();

// get data	
$row_count = 0;

$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$line = trim(fgets($file_handle));
	
	$row = explode("|",$line);

	$go = $line != "" && is_array($row) ;

	if ($go)
	{
		if ($row_count == 0)
		{
			$headings = $row;		
		}
		else
		{
			$obj = new stdclass;
		
			foreach ($row as $k => $v)
			{
				if ($v != '')
				{
					$obj->{$headings[$k]} = $v;
				}
			}
		
			print_r($obj);	
			
			// output link to taxon
			
			$taxon_uri = 'http://apps.kew.org/wcsp/namedetail.do?name_id=' . $obj->coreid;
			
			$location_uri = $obj->locationid;
			
			echo $taxon_uri . " -> " . $location_uri . "\n";
			
			// need a URI for the area, and need a way of distinguishing between native 
			// and non-native occurrences, perhaps using roles in schema.org?
			
		}
	}		

	$row_count++;	
	
	if ($row_count > 10)
	{
		exit();
	}
	

}

?>
