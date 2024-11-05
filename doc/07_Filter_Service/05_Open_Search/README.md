# Filter Service with OpenSearch

## Definition of Filter Types

When using OpenSearch as Product Index, different FilterTypes must be configured for the corresponding tenant. 
These filter types create the OpenSearch specific conditions for each filter.

Here is an example for the configuration: 
```yml
pimcore_ecommerce_framework:
    filter_service:
        tenants:
            MyOpenSearchTenant:
                filter_types:
                    FilterNumberRange:
                        # Service id for filter type implementation
                        filter_type_id: Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex\NumberRange
                        # Default template for filter, can be overwritten in filter definition
                        template: ':Shop/filters:range.html.php'

                    FilterNumberRangeSelection:
                        filter_type_id: Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex\NumberRangeSelection
                        template: ':Shop/filters:numberrange.html.php'

                    FilterSelect:
                        filter_type_id: Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex\Select
                        template: ':Shop/filters:select.html.php'

                    FilterSelectFromMultiSelect:
                        filter_type_id: Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex\SelectFromMultiSelect
                        template: ':Shop/filters:select.html.php'

                    FilterMultiSelect:
                        filter_type_id: Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex\MultiSelect
                        template: ':Shop/filters:multiselect.html.php'

                    FilterMultiSelectFromMultiSelect:
                        filter_type_id: Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex\MultiSelectFromMultiSelect
                        template: ':Shop/filters:multiselect.html.php'

                    FilterMultiRelation:
                        filter_type_id: Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex\MultiSelectRelation
                        template: ':Shop/filters:multiselect-relation.html.php'

                    FilterCategory:
                        filter_type_id: Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex\SelectCategory
                        template: ':Shop/filters:select_category.html.php'

                    FilterRelation:
                        filter_type_id: Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex\SelectRelation
                        template: ':Shop/filters:object_relation.html.php'
```

## Filtering for Classification Store Attributes

With the document structure of OpenSearch it is easily possible to index and filter for classification store attributes
without defining them all als separate attributes in the index definition. 
See [Filter Classification Store](01_Filter_Classification_Store.md) for details.  


## Filtering for Nested Documents in General
For information how to take leverage of the nested documents feature for OpenSearch and make even more sophisticated
search queries, have a look at [Filter Nested Documents](02_Filter_Nested_Documents.md). 