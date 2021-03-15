<?php
/**
 * Copyright (c) Victor Konchalenko
 */
namespace VictorKon\ZTest\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use VictorKon\ZTest\Api\Data\StatusInterface;

/**
 * Class for adding Z Test attribute to customer entity
 */
class AddZTestAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->addAttribute(
            Customer::ENTITY,
            StatusInterface::ZTEST_STATUS_ATTR_CODE,
            [
                'type' => 'varchar',
                'label' => 'Status',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'system' => false,
                'user_defined' => true,
                'sort_order' => 1000,
                'position' => 1000,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true
        ]);

        /** @var Magento\Eav\Model\Entity\Type $customerEntity */
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        /** @var integer $attributeSetId */
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var Magento\Eav\Model\Entity\Attribute\Set\Interceptor $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        /** @var integer $attributeGroupId*/
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        /** @var Magento\Customer\Model\Attribute @attribute */
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, StatusInterface::ZTEST_STATUS_ATTR_CODE);
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => [
                'adminhtml_customer',
                'customer_account_edit'
            ]
        ]);

        $attribute->save();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '1.0';
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
