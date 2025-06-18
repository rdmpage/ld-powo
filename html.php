<?php

// While we mess with web site thumbnails

require_once (dirname(__FILE__) . '/HtmlDomParser.php');
use Sunra\PhpSimple\HtmlDomParser;


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
function get_meta($url)
{
	$result = new stdclass;
	
	$html = get($url);	
		
	if ($html != '')
	{
		$dom = HtmlDomParser::str_get_html($html);
	
		if ($dom)
		{	
			// meta
			
			foreach ($dom->find('meta') as $meta)
			{
				if (isset($meta->name))
				{
					// Twitter
					switch ($meta->name)
					{				
						case 'twitter:image':
							if (!isset($result->thumbnailUrl))
							{
								$result->thumbnailUrl = $meta->content;
							}
							break;					

						case 'twitter:title':
							if (!isset($result->title))
							{
								$result->title = $meta->content;
							}
							break;					
							
						case 'twitter:url':
							if (!isset($result->url))
							{
								$result->url = $meta->content;
							}
							break;					

						default:
							break;
					}
				}
				
				if (isset($meta->property))
				{
					// Facebook
					switch ($meta->property)
					{				
						case 'og:image':
							if (!isset($result->thumbnailUrl))
							{
								$result->thumbnailUrl = $meta->content;
							}
							break;					

						case 'og:title':
							if (!isset($result->title))
							{
								$result->title = $meta->content;
							}
							break;	
							
						case 'og:url':
							if (!isset($result->url))
							{
								$result->url = $meta->content;
							}
							break;																

						default:
							break;
					}
				}
				
			}
		}
	}
	
	print_r($result);
	
	return $result;
}


$json = '{
    "@context": {
        "@vocab": "http://schema.org/",
        "rdf": "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
        "dwc": "http://rs.tdwg.org/dwc/terms/",
        "additionalType": {
            "@id": "additionalType",
            "@type": "@id",
            "@container": "@set"
        },
        "sameAs": {
            "@id": "sameAs",
            "@type": "@id",
            "@container": "@set"
        },
        "thumbnailUrl": {
            "@id": "thumbnailUrl",
            "@type": "@id"
        }
    },
    "@graph": [
        {
            "@id": "https://powo.science.kew.org/taxon/urn:lsid:ipni.org:names:251403-2",
            "@type": "Taxon",
            "alternateName": [
                "Polypodium lechleri Baker",
                "Amauropelta laevigata (Mett.) Salino & T.E.Almeida",
                "Phegopteris laevigata Mett.",
                "Dryopteris laevigata (Mett.) C.Chr.",
                "Polypodium laevigatum (Mett.) Baker"
            ],
            "alternateScientificName": [
                {
                    "@id": "urn:lsid:ipni.org:names:17173610-1",
                    "author": "Mett.",
                    "isBasedOn": {
                        "@id": "urn:lsid:ipni.org:names:17173610-1#13ce52c3f5ae3eb67155ea458a079a4f",
                        "name": "Linnaea 36: 112 (1869)"
                    },
                    "name": "Phegopteris laevigata",
                    "taxonRank": "species"
                },
                {
                    "@id": "urn:lsid:ipni.org:names:17096480-1",
                    "author": "(Mett.) C.Chr.",
                    "isBasedOn": {
                        "@id": "urn:lsid:ipni.org:names:17096480-1#783e30de5fd7b20c8a4c250278219618",
                        "name": "Index Filic.: 273 (1905)"
                    },
                    "name": "Dryopteris laevigata",
                    "taxonRank": "species"
                },
                {
                    "@id": "urn:lsid:ipni.org:names:207119-2",
                    "author": "Baker",
                    "isBasedOn": [
                        {
                            "@id": "https://doi.org/10.1093/oxfordjournals.aob.a090650",
                            "name": "A Summary of the new Ferns which have been discovered or described since 1874",
                            "thumbnailUrl": "https://academic.oup.com/data/sitebuilderassetsoriginals/live/images/aob/aob_ogimage.png"
                        },
                        {
                            "@id": "urn:lsid:ipni.org:names:207119-2#958d6cfe71a3114b7502f5429d0c74f8",
                            "name": "Ann. Bot. (Oxford) 5: 456 (1891)"
                        }
                    ],
                    "name": "Polypodium lechleri",
                    "taxonRank": "species"
                },
                {
                    "@id": "urn:lsid:ipni.org:names:17192290-1",
                    "author": "(Mett.) Baker",
                    "isBasedOn": {
                        "@id": "urn:lsid:ipni.org:names:17192290-1#7b8dde159a946d9a133602c7609426ce",
                        "name": "W.J.Hooker & J.G.Baker, Syn. Fil., ed. 2: 505 (1874)"
                    },
                    "name": "Polypodium laevigatum",
                    "taxonRank": "species"
                },
                {
                    "@id": "urn:lsid:ipni.org:names:77150994-1",
                    "author": "(Mett.) Salino & T.E.Almeida",
                    "isBasedOn": [
                        {
                            "@id": "urn:lsid:ipni.org:names:77150994-1#60f291bfdc03af648de668898cce009f",
                            "name": "PhytoKeys 57: 25 (2015)"
                        },
                        {
                            "@id": "https://doi.org/10.3897/phytokeys.57.5641",
                            "name": "New combinations in Neotropical Thelypteridaceae",
                            "thumbnailUrl": "https://phytokeys.pensoft.net//img/7TVeXpoqfNYT89tyrm3ifrTeG9Wv8P676JSQp/H2pj9hhtoybol4GF7LEbj3fxHT5Fo8esHss8AaY5Vmf0CDK1yHAaQmB4hJAI471Wyf.jpg"
                        }
                    ],
                    "name": "Amauropelta laevigata",
                    "taxonRank": "species"
                }
            ],
            "identifier": {
                "@id": "https://powo.science.kew.org/taxon/urn:lsid:ipni.org:names:251403-2#wcsp",
                "@type": "PropertyValue",
                "name": "WCSP",
                "value": "3168850"
            },
            "name": "Thelypteris laevigata (Mett.) R.M.Tryon",
            "parentTaxon": {
                "@id": "https://powo.science.kew.org/taxon/urn:lsid:ipni.org:names:331118-2"
            },
            "sameAs": [
                "http://apps.kew.org/wcsp/namedetail.do?name_id=3168850"
            ],
            "scientificName": {
                "@id": "urn:lsid:ipni.org:names:251403-2",
                "author": "(Mett.) R.M.Tryon",
                "isBasedOn": {
                    "@id": "urn:lsid:ipni.org:names:251403-2#e78d9d1c37b7a17a3c59a920874c9ba2",
                    "name": "Rhodora 69: 6 (1967)"
                },
                "name": "Thelypteris laevigata",
                "taxonRank": "species"
            }
        }
    ]
}';


//----------------------------------------------------------------------------------------
function to_array($value)
{
	if (is_array($value))
	{
		return $value;
	}
	else
	{
		return array($value);
	}

}

//----------------------------------------------------------------------------------------
function display_isBasedOn($node)
{
	$values = to_array($node);
	
	$format = 'string';
	
	if (count($values) > 1)
	{
		$format = 'list';
	}
	
	if ($format == 'list')
	{
		echo '<ul>';
	}
	
	foreach ($values as $v)
	{		
		if ($format == 'list')
		{
			echo '<li>';
		}
														
		if (preg_match('/doi.org/', $v->{'@id'}))
		{
			echo '<a href="' . $v->{'@id'} . '">';
		}
	
		if (isset($v->name))
		{
			if ($format == 'string')
			{	
				echo ' in ';
			}
			echo $v->name;
		}
		
		if (preg_match('/doi.org/', $v->{'@id'}))
		{
			echo '</a>';
		}
		
		if (isset($v->thumbnailUrl))
		{
			echo '<br /><img width="80" src="' . $v->thumbnailUrl . '">';
		}
		
		
		
		if ($format == 'list')
		{
			echo '</li>';
		}
		
	}	
	
	if ($format == 'list')
	{
		echo '</ul>';
	}

}

//----------------------------------------------------------------------------------------


$obj = json_decode($json);


// output...

echo '<html>
<head>
<style>
body {
	padding: 1em;
	font-family:sans-serif;
}
li {
	padding:0.5em;
}
.code {
    font-family:monospace;
    white-space:pre-wrap; /* pre-wrap means text wraps, pre by itself does not wrap */
    color:rgb(192,192,192);
}
</style>
</head>
<body>';

$root = $obj->{'@graph'}[0];

// breadcrumbs

// title is name
if (isset($root->name))
{
	echo '<h1>' . $root->name . '</h1>';
}

// scientific name
if (isset($root->scientificName))
{
	echo '<div>';
	
	echo '<a href="' . $root->scientificName->{'@id'} . '">';
	
	if (isset($root->scientificName->name))
	{
		echo $root->scientificName->name;			
	}

	if (isset($root->scientificName->author))
	{
		echo ' ' . $root->scientificName->author;			
	}

	echo '</a>';


	if (isset($root->scientificName->isBasedOn))
	{
		display_isBasedOn($root->scientificName->isBasedOn);
	}	
	
	echo '</div>';
}

echo '<h2>Distribution</h2>';

echo '<h2>Synonyms</h2>';

if (isset($root->alternateScientificName))
{
	echo '<ul>';
	
	foreach ($root->alternateScientificName as $alternateScientificName)
	{
		echo '<li>';
		echo '<a href="' . $alternateScientificName->{'@id'} . '">';
		
		if (isset($alternateScientificName->name))
		{
			echo $alternateScientificName->name;			
		}

		if (isset($alternateScientificName->author))
		{
			echo ' ' . $alternateScientificName->author;			
		}

		echo '</a>';
		
		if (isset($alternateScientificName->isBasedOn))
		{
			display_isBasedOn($alternateScientificName->isBasedOn);
		}
		
		echo '</li>';
	}
	
	echo '</ul>';
}

echo '<h2>Classification</h2>';


if (1)
{
	echo '<h2>Data</h2>';
	echo '<div class="code">';
	echo json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	echo '</div>';
}


echo '</body>';
echo '</html>';

?>
