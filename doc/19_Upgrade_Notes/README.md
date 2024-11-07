# Upgrade Notes

## Version 2.0.0

### [Elasticsearch]

- Removed ElasticSearchConfigInterface, use SearchConfigInterface instead.
- Removed EsSyncCommand it is now IndexSyncCommand. Use the ecommerce:indexservice:search-index-sync command instead.
- Introduced IndexRefreshInterface for refresh command.
- Moved ElasticSearch Filter Types to SearchIndex namespace. Make sure to update your filter namespaces in your yaml configuration.