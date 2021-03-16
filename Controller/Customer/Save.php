<?php
/**
 * Copyright (c) Victor Konchalenko
 */
declare(strict_types=1);

namespace VictorKon\ZTest\Controller\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\SessionCleanerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Customer\Controller\AbstractAccount;
use VictorKon\ZTest\Api\Data\StatusInterface;

/**
 * Customer status save controller
 */
class Save extends \VictorKon\ZTest\Controller\Customer implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CustomerRepository $customerRepository
    ) {
        parent::__construct($context, $customerSession);
        $this->storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Save the form data
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var boolean $validFormKey */
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());
        if ($validFormKey && $this->getRequest()->isPost()) {
            /** @var integer $customerId */
            $customerId = $this->customerSession->getCustomerId();
            if (!$customerId) {
                $this->messageManager->addErrorMessage(__('Unknown customer'));
            } else {
                /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
                $customer = $this->customerRepository->getById($customerId);

                try {
                    $customer->setCustomAttribute(StatusInterface::ZTEST_STATUS_ATTR_CODE,
                        $this->getRequest()->getParam(StatusInterface::ZTEST_STATUS_ATTR_CODE)
                    );

                    // Set ignore_validation_flag to skip unnecessary address and customer validation
                    $customer->setData('ignore_validation_flag', true);

                    $this->customerRepository->save($customer);
                    $this->_eventManager->dispatch(
                        'customer_account_edited',
                        ['email' => $customer->getEmail()]
                    );
                    $this->messageManager->addSuccessMessage(__('Information saved'));
                    return $resultRedirect->setPath('*/*/index');
                } catch (UserLockedException $e) {
                    $message = __(
                        'The account sign-in was incorrect or your account is disabled temporarily. '
                        . 'Please wait and try again later.'
                    );
                    $this->customerSession->logout();
                    $this->customerSession->start();
                    $this->messageManager->addErrorMessage($message);

                    return $resultRedirect->setPath('customer/account/login');
                } catch (InputException $e) {
                    $this->messageManager->addErrorMessage($this->escaper->escapeHtml($e->getMessage()));
                    foreach ($e->getErrors() as $error) {
                        $this->messageManager->addErrorMessage($this->escaper->escapeHtml($error->getMessage()));
                    }
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('The information can\'t be saved'));
                }

                $this->customerSession->setCustomerFormData($this->getRequest()->getPostValue());
            }
        }

        $resultRedirect->setPath('*/*/index');

        return $resultRedirect;
    }

    /**
     * Set ignore_validation_flag to skip unnecessary address and customer validation
     *
     * @param CustomerInterface $customer
     * @return void
     */
    private function setIgnoreValidationFlag(CustomerInterface $customer): void
    {
        $customer->setData('ignore_validation_flag', true);
    }
}
