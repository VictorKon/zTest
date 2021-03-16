<?php
/**
 * Copyright (c) Victor Konchalenko
 */
namespace VictorKon\ZTest\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use VictorKon\ZTest\Api\Data\StatusInterface;

/**
 * Customer data provider
 */
class Ztest implements SectionSourceInterface
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData()
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }

        /** @var \Magento\Customer\Api\Data\CustomerInterface */
        $customer = $this->currentCustomer->getCustomer();

        /** @var \Magento\Framework\Api\AttributeInterface|null $attribute */
        $attribute = $customer->getCustomAttribute(StatusInterface::ZTEST_STATUS_ATTR_CODE);

        return [
            'status' => $attribute ? $attribute->getValue() : null
        ];
    }
}
