<?php
/**
 * Copyright (c) 2012-1013, Magentix
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *  - Neither the name of the Magentix nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * @copyright Copyright 2012, Magentix (http://www.magentix.fr)
 * @license http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * 
 * @category Solr
 * @package Magentix_Solr
 * @author Matthieu Vion <contact@magentix.fr>
 * @contributor Nicolas Trossat <http://www.boutikcircus.com>
 */

class Magentix_Solr_Model_CatalogSearch_Resource_Fulltext extends Mage_CatalogSearch_Model_Resource_Fulltext
{
    
    /**
     * Overloaded method prepareResult.
     * Prepare results for query.
     * Replaces the traditional fulltext search with a Solr Search (if active).
     *
     * @param Mage_CatalogSearch_Model_Fulltext $object
     * @param string $queryText
     * @param Mage_CatalogSearch_Model_Query $query
     * @return Magentix_Solr_Model_CatalogSearch_Resource_Fulltext
     */
    public function prepareResult($object, $queryText, $query)
    {
        if(!Mage::getStoreConfigFlag('solr/active/frontend')) {
            return parent::prepareResult($object, $queryText, $query);
        }
        
        $adapter = $this->_getWriteAdapter();
        if (!$query->getIsProcessed()) {
            
            try {
                $search = Mage::getModel('solr/search')
                          ->loadQuery($queryText,(int)$query->getStoreId(),(int)Mage::getStoreConfig('solr/search/limit'));
                
                if($search->count()) {
                    $products = $search->getProducts();

                    $data = array();
                    foreach($products as $product) {
                        $data[] = array('query_id'   => $query->getId(),
                                        'product_id' => $product['product_id'],
                                        'relevance'  => $product['relevance']);
                    }

                    $adapter->insertMultiple($this->getTable('catalogsearch/result'),$data);
                }

                $query->setIsProcessed(1);
                
            } catch (Exception $e) {
                Mage::log($e->getMessage(),3,Mage::helper('solr')->getLogFile());
                return parent::prepareResult($object, $queryText, $query);
            }
            
        }

        return $this;
    }
    
    /**
     * Overloaded method rebuildIndex.
     * Regenerate search index for store(s)
     *
     * @param  int|null $storeId
     * @param  int|array|null $productIds
     * @return Magentix_Solr_Model_CatalogSearch_Resource_Fulltext
     */
    public function rebuildIndex($storeId = null, $productIds = null)
    {
        parent::rebuildIndex($storeId,$productIds);

        if(Mage::getStoreConfigFlag('solr/active/admin')) {
            Mage::getModel('solr/indexer')->rebuildIndex($productIds);
        }

        return $this;
    }
    
    /**
     * Overloaded method cleanIndex.
     * Delete search index data for store
     *
     * @param int $storeId Store View Id
     * @param int|array|null $productIds Product Entity Id
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function cleanIndex($storeId = null, $productIds = null)
    {
        parent::cleanIndex($storeId, $productIds);
        
        if(Mage::getStoreConfigFlag('solr/active/admin')) {
            Mage::getModel('solr/indexer')->cleanIndex($productIds);
        }

        return $this;
    }
    
}