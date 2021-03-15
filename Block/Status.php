<?php
/**
 * Copyright (c) Victor Konchalenko
 */
namespace VictorKon\ZTest\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use VictorKon\ZTest\Api\Data\StatusInterface;

/**
 * Customer area ZTest block
 */
class Status extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if ($this->customerSession->isLoggedIn()) {
            return parent::_toHtml();
        }
        return '';
    }
}
