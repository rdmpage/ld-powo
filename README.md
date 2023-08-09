# Linked data version of Plants of the World Online (POWO)

Encoding Kew’s [Plants of the World Online](https://powo.science.kew.org) as linked data.


## Background

POWO is Kew’s portal to plant taxonomy, URLs for taxa are based on IPNI LSIDs for the accepted name for that taxon, and taxa are linked to IPNI names. The data is available from ChecklistBank as [World Checklist of Vascular Plants](https://www.checklistbank.org/dataset/2000) and [PoWO Family Taxonomy](https://www.checklistbank.org/dataset/2001). 

The naming of Kew’s databases is somewhat confusing. POWO is either a rebranding of the [World Checklist of Vascular Plants](https://powo.science.kew.org/about-wcvp) (WCVP), or simply an interface to WCVP. However, links to WCVP entries (such as http://apps.kew.org/wcsp/namedetail.do?name_id=3168851) now redirect to the POWO home page.


## Data sources

### ChecklistBank

ChecklistBank has data for POWO. The complete checklist (World Checklist of Vascular Plants 2023-06-28) is available here [powoNames.zip](https://storage.googleapis.com/powop-content/backbone/powoNames.zip). The family taxonomy (PoWO Family Taxonomy 2023-06-28) is available here:[powoPlantFamilies.zip](https://storage.googleapis.com/powop-content/backbone/powoPlantFamilies.zip). Copies of these files are in the `data` folder.

The POWO files include a link to WCVP as the `source`, which enables us to map identifiers between the two databases.

### POWO

POWO provides data dumps from http://sftp.kew.org/pub/data-repositories/WCVP, I have downloaded [wcvp_dwca.zip](http://sftp.kew.org/pub/data-repositories/WCVP/wcvp_dwca.zip) dated 2023-04-27. These files include data on distribution which is not available in the POWO data sent to ChecklistBank.

### Geography

WCVP provides distributional data using World Geographical Scheme for Recording Plant Distributions (WGSRPD) codes for Level-3 areas. GeoJSON for these codes is available from GitHub [here](https://github.com/rdmpage/prior-standards/blob/master/world-geographical-scheme-for-recording-plant-distributions/geojson/level3.geojson) and [here](https://github.com/tdwg/wgsrpd/blob/master/geojson/level3.geojson). I have downloaded the Level-3 data to `data/wgsrpd`.

## RDF

### Modelling taxa and names


### Modelling geography

In the absence of persistent identifiers for the regions, could “fake” them using `https://github.com/tdwg/wgsrpd/blob/master/geojson/level3.geojson#` as the namespace, with each regions as a fragment identifier.

Note that there is a pull request to change the GeoJSON files: https://github.com/tdwg/wgsrpd/pull/10


### Upload triples

For small-scale experiments can load directly:

```
curl 'http://localhost:7878/store?graph=https://powo.science.kew.org' --header Content-Type:application/n-triples --data-binary @powo.nt
```

## Queries

### Describe a taxon

```
DESCRIBE <https://powo.science.kew.org/taxon/urn:lsid:ipni.org:names:251403-2>
```

### People who worked on a taxon with links to images

```
PREFIX schema: <http://schema.org/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>

SELECT DISTINCT ?creator_name ?creator ?rg_profile ?rg_image_url 
FROM <https://powo.science.kew.org>
FROM <https://www.ipni.org>
FROM <https://orcid.org>
FROM <https://www.researchgate.net>
WHERE {
  # taxon
  ?taxon rdf:type schema:Taxon .
  {
      ?taxon schema:scientificName ?scientificName .
  }
  UNION
  {
      ?taxon schema:alternateScientificName ?scientificName .
  }  
  # names associated with taxon
  ?scientificName schema:name ?name .
  
  # works publishing names, and creators of those works
  ?scientificName schema:isBasedOn ?work .
  ?work schema:creator ?creator .
  {
    ?creator schema:givenName ?givenName .
    ?creator schema:familyName ?familyName .
    BIND(CONCAT(?givenName, " ", ?familyName) AS ?creator_name).
  }
  UNION
  {
    ?creator schema:name ?creator_name .
  }
  
  # socials for creators
  OPTIONAL
  {
    ?rg schema:sameAs ?creator .
    ?rg schema:mainEntityOfPage ?rg_profile .
    ?rg schema:image ?rg_image .
    ?rg_image schema:contentUrl ?rg_image_url .
  }
} 
```


## Reading

Govaerts, R., Nic Lughadha, E., Black, N. et al. The World Checklist of Vascular Plants, a continuously updated resource for exploring global plant diversity. Sci Data 8, 215 (2021). https://doi.org/10.1038/s41597-021-00997-6

Schellenberger Costa, D., Boehnisch, G., Freiberg, M., Govaerts, R., Grenié, M., Hassler, M., Kattge, J., Muellner-Riehl, A.N., Rojas Andrés, B.M., Winter, M., Watson, M., Zizka, A. and Wirth, C. (2023), The big four of plant taxonomy – a comparison of global checklists of vascular plant names. New Phytol. https://doi.org/10.1111/nph.18961


