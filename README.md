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


## Reading

Govaerts, R., Nic Lughadha, E., Black, N. et al. The World Checklist of Vascular Plants, a continuously updated resource for exploring global plant diversity. Sci Data 8, 215 (2021). https://doi.org/10.1038/s41597-021-00997-6

Schellenberger Costa, D., Boehnisch, G., Freiberg, M., Govaerts, R., Grenié, M., Hassler, M., Kattge, J., Muellner-Riehl, A.N., Rojas Andrés, B.M., Winter, M., Watson, M., Zizka, A. and Wirth, C. (2023), The big four of plant taxonomy – a comparison of global checklists of vascular plant names. New Phytol. https://doi.org/10.1111/nph.18961


