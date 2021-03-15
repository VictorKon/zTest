<?php
/**
 * Copyright (c) Victor Konchalenko
 */
namespace VictorKon\ZTest\Block\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use VictorKon\ZTest\Api\Data\StatusInterface;

/**
 * Customer area ZTest block
 */
class Form extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * @return string
     */
    public function getZTestStatus()
    {
        return $this->getCustomer()->getCustomAttribute(StatusInterface::ZTEST_STATUS_ATTR_CODE)->getValue();
    }

    /**
     * Return the save action Url.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getUrl('ztest/customer/save');
    }
}
