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
        /** @var \Magento\Framework\Api\AttributeInterface|null $attribute */
        $attribute = $this->getCustomer()->getCustomAttribute(StatusInterface::ZTEST_STATUS_ATTR_CODE);

        return $attribute ? $attribute->getValue() : null;
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
