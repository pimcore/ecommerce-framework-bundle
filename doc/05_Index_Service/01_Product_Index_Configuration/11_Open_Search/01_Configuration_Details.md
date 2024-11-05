# Configuration Configuration

Following aspects need to be considered in index configuration:  

## General Configuration Options
In the `config_options` area general OpenSearch settings can be made - like hosts, index settings, etc. 

##### `client_config`
- `indexName`: index name to be used, if not provided tenant name is used as index name 

##### `index_settings`
Index settings that are used when creating a new index. They are passed 1:1 as 
settings param to the body of the create index command. Details see 
also [OpenSearch Docs](https://opensearch.org/docs/latest/api-reference/index-apis/index/). 

#### `opensearch_client_name`
OpenSearch client configuration takes place via 
[Pimcore OpenSearch Client Bundle](https://github.com/pimcore/opensearch-client) and has two parts.

1) Configuring an OpenSearch client in separate configuration
```yaml
# Configure an OpenSearch client 
pimcore_open_search_client:
    clients:
        default:
            hosts: [ 'opensearch:9200' ]
            username: 'admin'
            password: 'somethingsecret'
            logger_channel: 'pimcore.opensearch'    
```

2) Define the client name to be used by an OpenSearch tenant. This will be done via the `opensearch_client_name` configuration 
   in the `config_options`. 

##### `synonym_providers`
Specify synonym providers for synonym filters defined in filter section of index settings. 
For details see [Synonyms](./02_Synonyms.md).

#### Sample Config
```yml
pimcore_ecommerce_framework:
    index_service:
        tenants:
            MyOpenSearchTenant:
                worker_id: Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\OpenSearch\DefaultOpensearch
                config_id: Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config\OpenSearch
                
                config_options:
                    client_config:
                        indexName: 'ecommerce-demo-opensearch'

                    opensearch_client_name: default

                    index_settings:
                        number_of_shards: 5
                        number_of_replicas: 0
                        max_ngram_diff: 30
                        analysis:
                            analyzer:
                                my_ngram_analyzer:
                                    tokenizer: my_ngram_tokenizer
                                allow_list_analyzer:
                                    tokenizer: standard
                                    filter:
                                      - allow_list_filter
                            tokenizer:
                                my_ngram_tokenizer:
                                    type: ngram
                                    min_gram: 2
                                    max_gram: 15
                                    token_chars: [letter, digit]
                            filter:
                                allow_list_filter:
                                    type: keep
                                    keep_words:
                                      - was
                                      - WAS
```


## Data Types for attributes
The type of the data attributes needs to be set to OpenSearch data types..

```yml
pimcore_ecommerce_framework:
    index_service:
        tenants:
            MyOpenSearchTenant:
                attributes:
                    name:
                        locale: '%%locale%%'
                        type: keyword
```

In addition to the `type` configuration, you also can provide custom mappings for a field. If provided, these mapping 
configurations are used for creating the mapping of the OpenSearch index.

You can also skip the `type` and `mapping`, then OpenSearch will try to create dynamic mapping. 

```yml

pimcore_ecommerce_framework:
    index_service:
        tenants:
            MyOpenSearchTenant:
                attributes:
                    name:
                        locale: '%%locale%%'
                        type: text
                        options:
                            mapping:
                                type: text
                                store: true
                                index: not_analyzed
                                fields:
                                    analyzed:
                                        type: text
                                        analyzer: german
                                    analyzed_ngram:
                                        type: text
                                        analyzer: my_ngram_analyzer
``` 
