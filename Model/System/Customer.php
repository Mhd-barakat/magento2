<?php

/**
 * @author   dzgok  <dgokdunek@tmobtech.com>
 * @license  https://raw.githubusercontent.com/tappz/magento2/master/LICENCE
 *
 * @link     http://t-appz.com/
 */

namespace TmobLabs\Tappz\Model\System;

/**
 * Class Customer.
 */
class Customer implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Payment\Helper\Data
     */
    private $storeHelper;

    /**
     * Customer constructor.
     *
     * @param \Magento\Payment\Helper\Data $storeHelper
     */
    public function __construct(\Magento\Payment\Helper\Data $storeHelper)
    {
        $this->storeHelper = $storeHelper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $options[] = ['value' => ' ', 'label' => ' '];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attributes = $objectManager->
        get('Magento\Customer\Model\Customer')->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisible()) {
                $options[] = [
                    'value' => $attribute->getAttributeCode(),
                    'label' => $attribute->getFrontendLabel(),
                ];
            }
        }

        return $options;
    }
}
