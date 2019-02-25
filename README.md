E-CONOMIX Database Translation Magento2 Module
============
 
 ![Magento 2](https://img.shields.io/badge/Magento-%3E=2.2-blue.svg)
 ![PHP >= 7.0.13](https://img.shields.io/badge/PHP-%3E=7.0.13-green.svg)
 
Magento2 module to add and edit database translation within the backend.

Installation
------------

The easiest way to install the extension is to use [Composer](https://getcomposer.org/).

Run the following commands:

- ```$ composer require e-conomix/module-dbtranslations```
- ```$ bin/magento module:enable Economix_DbTranslations```
- ```$ bin/magento setup:upgrade && bin/magento setup:static-content:deploy```

Attention
-----------
:exclamation: These translations should not be used if not absolutely necessary! Create translations within module, language pack or theme instead. :exclamation:

Features
------------

Create and edit existing database translations from the backend, go to:

    Content -> Translations -> DB Translations
    
![alt translation menu entry](docs/images/menu_entry.png)

*List view*
![alt translation list view](docs/images/list.png)

*Edit view*
![alt translation edit view](docs/images/edit.png)

*Create view*
![alt translation create view](docs/images/create.png)