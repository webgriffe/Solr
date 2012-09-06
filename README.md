Description
-----------

Make search on Magento CE with Apache Solr.

Based on PHP library for Apache Solr : [solr-php-client](http://code.google.com/p/solr-php-client/)

* Extension use fulltext for index. You can search in any attributes (Use in Quick Search).

* If Apache Tomcat server is down, usual search is used. It is invisible for user.

* Search relevance is stored (Magento do not use relevance with MySQL fulltext).

* Extension do not support catalog advanced search. Usual search is used for this feature.

* Compatibility : Magento >= 1.5


Configuration
-------------

* Config
 * System > Configuration > Catalog > Solr Search Engine