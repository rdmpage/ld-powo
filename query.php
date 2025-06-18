<?php

// Queries to results

require_once(dirname(__FILE__) . '/vendor/autoload.php');

use ML\JsonLD\JsonLD;
use ML\JsonLD\NQuads;


//----------------------------------------------------------------------------------------
function get($url, $format = '')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	if ($format != '')
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: " . $format));	
	}
	
	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	curl_close($ch);
	
	return $response;
}

//----------------------------------------------------------------------------------------
function post($url, $data = '', $content_type = '', $accept = '')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	$header = array();
	
	if ($content_type != '')
	{
		$header[] = "Content-type: " . $content_type;
	}
	if ($accept != '')
	{
		$header[] = "Accept: " . $accept;
	}
		
	if (count($header) != 0)
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
		
	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
		
	curl_close($ch);
	
	return $response;
}

$uri = 'https://powo.science.kew.org/taxon/urn:lsid:ipni.org:names:251403-2';

$sparql = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX schema: <http://schema.org/>

CONSTRUCT
{
  ?taxon a ?type .
  ?taxon schema:name ?name .
  
  ?taxon schema:parentTaxon ?parent .
  
  # scientific name
	?taxon schema:scientificName ?scientificName .
	?scientificName schema:name ?scientificNameString .
	?scientificName schema:author ?author .
	?scientificName schema:taxonRank ?rank .
	?scientificName schema:isBasedOn ?pub . 
	?pub schema:name ?pubName . 
	?pub schema:thumbnailUrl ?pubThumbnailUrl .  

	
	# sameAs
	?taxon schema:sameAs ?sameAs .  
	
	# identifier
	?taxon schema:identifier ?identifier .
    ?identifier rdf:type schema:PropertyValue .
	?identifier schema:name ?identifierName .
	?identifier schema:propertyID ?propertyID .
	?identifier schema:value ?identifierValue .	
  
  # alternate names as strings
  ?taxon schema:alternateName ?alternateName .
  
  # alternate names as things
  ?taxon schema:alternateScientificName ?alternateScientificName .
  ?alternateScientificName schema:name ?alternateNameString .
  ?alternateScientificName schema:taxonRank ?alternateRank .
  ?alternateScientificName schema:author ?alternateAuthor .
  ?alternateScientificName schema:isBasedOn ?alternatePub .
  ?alternatePub schema:name ?alternatePubName .  
  ?alternatePub schema:thumbnailUrl ?alternatePubThumbnailUrl .  

}
WHERE {
  VALUES ?taxon { <' . $uri . '> }
	?taxon a ?type .
    ?taxon schema:name ?name .
  
  OPTIONAL { ?taxon schema:parentTaxon ?parent . }
  
	?taxon schema:scientificName ?scientificName .
	?scientificName schema:name ?scientificNameString .
	?scientificName schema:author ?author .
	?scientificName schema:taxonRank ?rank .
	OPTIONAL 
	{
		?scientificName schema:isBasedOn ?pub . 
		?pub schema:name ?pubName . 
		OPTIONAL {
		?pub schema:thumbnailUrl ?pubThumbnailUrl .  
		}
	}   
	
	OPTIONAL { ?taxon schema:sameAs ?sameAs . } 
	
	OPTIONAL {
	?taxon schema:identifier ?identifier .
	?identifier schema:name ?identifierName .
	#?identifier schema:propertyID ?propertyID .
	?identifier schema:value ?identifierValue .
	}
	
	
    OPTIONAL { ?taxon schema:alternateName ?alternateName . }
  
    OPTIONAL {
    ?taxon schema:alternateScientificName ?alternateScientificName .
    ?alternateScientificName schema:name ?alternateNameString .
    ?alternateScientificName schema:taxonRank ?alternateRank .
    OPTIONAL
    {
    	?alternateScientificName schema:author ?alternateAuthor .
    }  
    OPTIONAL
    {
      ?alternateScientificName schema:isBasedOn ?alternatePub .
      ?alternatePub schema:name ?alternatePubName .
      OPTIONAL {
		?alternatePub schema:thumbnailUrl ?alternatePubThumbnailUrl .  
      }
    }  
  }
  
}';


if (0)
{
	// A SPARQL query
	$sparql = 'SELECT * WHERE {?s ?p ?o . } LIMIT 10';

	$data = 'query=' . $sparql;

	$json = post(
		$sparql_endpoint,
		$data,
		'application/x-www-form-urlencoded',
		'application/sparql-results+json'
		);

	echo $json;
}

if (1)
{
	// A SPARQL query

	$sparql_endpoint = 'http://localhost:7878/query?union-default-graph';

	$data = 'query=' . $sparql;

	$triples = post(
		$sparql_endpoint,
		$data,
		'application/x-www-form-urlencoded',
		'application/n-triples'
		);

	//echo $triples;
	
	$triple_array = explode("\n", $triples);
	
	print_r($triple_array);
	
	$triple_array = array_unique($triple_array);
	
	print_r($triple_array);
	
	$triples = join("\n", $triple_array);
	
	// to JSON-LD
	

	$context = new stdclass;
	$context->{'@vocab'} = 'http://schema.org/';
	$context->rdf =  "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
	$context->dwc =  "http://rs.tdwg.org/dwc/terms/";

	$additionalType = new stdclass;
	$additionalType->{'@id'} = "additionalType";
	$additionalType->{'@type'} = "@id";
	$additionalType->{'@container'} = "@set";

	$context->{'additionalType'} = $additionalType;

	$sameAs = new stdclass;
	$sameAs->{'@id'} = "sameAs";
	$sameAs->{'@type'} = "@id";
	$sameAs->{'@container'} = "@set";
	$context->{'sameAs'} = $sameAs;			

	$thumbnailUrl = new stdclass;
	$thumbnailUrl->{'@id'} = "thumbnailUrl";
	$thumbnailUrl->{'@type'} = "@id";
	$context->{'thumbnailUrl'} = $thumbnailUrl;			

	// Frame document
	$frame = (object)array(
		'@context' => $context,
		'@type' => 'http://schema.org/Taxon'
	);	

	// Use same libary as EasyRDF but access directly to output ordered list of authors
	$nquads = new NQuads();
	// And parse them again to a JSON-LD document
	$quads = $nquads->parse($triples);		
	$doc = JsonLD::fromRdf($quads);

	$obj = JsonLD::frame($doc, $frame);

	echo json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

?>
