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
 */
class Magentix_Solr_Adminhtml_SolrController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Rebuild action
     */
    public function rebuildAction()
    {
        $session = Mage::getSingleton('adminhtml/session');

        if (Mage::getModel('solr/indexer')->rebuildIndex()) {
            $session->addSuccess(Mage::helper('solr')->__('Data have been successfully built.'));
        } else {
            $session->addNotice(Mage::helper('solr')->__('Can not index data in Solr, please see log for details.'));
        }

        $this->_redirect('*/system_config/edit', array('section' => 'solr'));

        return;
    }

    /**
     * Clean action
     */
    public function cleanAction()
    {
        $session = Mage::getSingleton('adminhtml/session');

        if (Mage::getModel('solr/indexer')->cleanIndex()) {
            $session->addSuccess(Mage::helper('solr')->__('Data have been successfully deleted.'));
        } else {
            $session->addNotice(Mage::helper('solr')->__('Can not delete data in Solr, please see log for details.'));
        }

        $this->_redirect('*/system_config/edit', array('section' => 'solr'));

        return;
    }

    /**
     * Ping action
     */
    public function pingAction()
    {
        $session = Mage::getSingleton('adminhtml/session');

        /** @var Apache_Solr_Service $solrService */
        $solrService = Mage::getModel('solr/search');
        if ($solrService->ping()) {
            $session->addSuccess(Mage::helper('solr')->__('Ping was successful.'));
        } else {
            $session->addError(
                Mage::helper('solr')->__(
                    'Cannot ping Solr server. Check that the settings are correct and that the' .
                    ' Solr service is active and running.'
                )
            );
        }

        $this->_redirect('*/system_config/edit', array('section' => 'solr'));

        return;
    }

}