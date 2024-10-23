# Special Aspects for OpenSearch
Basically OpenSearch worker works as described in the [optimized architecture](../README.md).

## Installation

### OpenSearch
To work properly Pimcore requires the OpenSearch client, install them with: `composer require pimcore/opensearch-client`.

## Index Configuration
OpenSearch provides a couple of additional configuration options for the index to utilize OpenSearch features. 
See [Configuration Details](01_Configuration_Details.md) for more information. 

## Reindexing Mode
It is possible that OpenSearch cannot update the mapping, e.g. if data types of attributes change on the fly. 
For this case, a reindex is necessary. If it is necessary, a native ES reindex is executed automatically during
`bin/console ecommerce:indexservice:bootstrap --create-or-update-index-structure`.

While reindex is executed, no updates are written to the OpenSearch index. The changes remain in store table and are transferred
to index during next execution of `bin/console ecommerce:indexservice:process-update-queue` after reindex is finished. 

All queries that take place during reindex go to the old index. As soon the reindex is finished, the current index is switched 
to the newly created index and the old index is deleted.  
As a result, during reindex the results delivered by Product Lists can contain old data. 

To manually start a reindex, following command can be used: `bin/console ecommerce:indexservice:search-index-sync reindex`.

## Indexing of Classification Store Attributes

With OpenSearch it is possible to index all attributes of [Classification Store](https://github.com/pimcore/pimcore/tree/11.x/doc/05_Objects/01_Object_Classes/01_Data_Types/15_Classification_Store.md) 
data without defining an attribute for each single classification store key.   

For details see [Filter Classification Store](../../../07_Filter_Service/03_Elastic_Search/01_Filter_Classification_Store.md) 
in Filter Service documentation. 

## Synonyms 
Pimcore provides an out-of-the box integration for synonyms in OpenSearch. 
See [Synonyms](./02_Synonyms.md) for details. 