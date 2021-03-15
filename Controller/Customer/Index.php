<?php
/**
 * Copyright (c) Victor Konchalenko
 */
namespace VictorKon\ZTest\Controller\Customer;

use Magento\Framework\App\RequestInterface;

/**
 * Controller for the customer area form
 */
class Index extends \VictorKon\ZTest\Controller\Customer
{
    /**
     * Show the form page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();

        if ($block = $this->_view->getLayout()->getBlock('ztest_customer_form')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->getPage()->getConfig()->getTitle()->set(__('Z Test Form'));
        $this->_view->renderLayout();
    }
}
