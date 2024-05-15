# Module for Customer Imports

## The Nitesh_CustomerImports module is responsible for managing the import of customer data.

## Requirements
- PHP 8.2+
- Magento 2.4.7 

## Installation
 - Unzip the zip file in `app/code`
 - Enable the module by running `php bin/magento module:enable Nitesh_CustomerImports`
 - Apply database updates by running `php bin/magento setup:upgrade`

### CLI commands
 - CSV profile - Place CSV inside var/import/ folder -     
    php bin/magento customer:import csv var/import/sample.csv
 
 - JSON profile - Place json inside var/import/ folder -   
    php bin/magento customer:import json var/import/sample.json

### Once run customer imports script, make sure to re-index the Customer Grid indexer 
 - php bin/magento indexer:reindex customer_grid 


