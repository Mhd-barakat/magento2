<?php

/**
 * @author   dzgok  <dgokdunek@tmobtech.com>
 * @license  https://raw.githubusercontent.com/tappz/magento2/master/LICENCE
 *
 * @link     http://t-appz.com/
 */

namespace TmobLabs\Tappz\Model\Product;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use TmobLabs\Tappz\API\Data\ProductInterface;

class ProductCollector extends ProductFill implements ProductInterface
{
    protected $product;
    protected $scopeConfigInterface;
    protected $objectManager;
    protected $categoryFactory;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfigInterface,
        CategoryFactory $categoryFactory
    ) {
        parent::__construct($storeManager);
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->categoryFactory = $categoryFactory;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function getProduct($productId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->product = $objectManager->
        get('Magento\Catalog\Model\Product')->
        load($productId);

        return $this->fillProduct();
    }

    public function getProductBySku($barcode)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productId = $objectManager->
        get('Magento\Catalog\Model\Product')->getIdBySku($barcode);
        $this->product = $product = $objectManager->
        get('Magento\Catalog\Model\Product')->load($productId);

        return $this->fillProduct();
    }

    public function getRelatedProduct($productId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->product = $objectManager->get('Magento\Catalog\Model\Product')->
        load($productId);
        $result = array();
        if ($this->product->hasRelatedProductIds()) {
            foreach ($this->product->getRelatedProducts() as $product) {
                $this->product = $this->getProduct($product->getID());
                $result[] = $this->fillProduct();
            }
        }

        return $result;
    }

    public function getProductSearch($params)
    {
        if (isset($params['category']) && !empty($params['category'])) {
            $layer = $this->objectManager->
            get('Magento\Catalog\Model\CategoryFactory')->
            create()->load($params['category']);
            $category = $this->objectManager->
            get('Magento\Catalog\Model\Category')->
            load($params['category']);
            $layer->setCurrentCategory($category);
            $productCollection = $layer->
            getProductCollection()->
            addAttributeToSelect('*')->
            addPriceData()->
            addAttributeToFilter('status', '1');
        } else {
            $object = \Magento\Framework\App\ObjectManager::getInstance();
            $productCollection = $object->
            create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        }
        if (!isset($params['pageSize'])
            || empty($params['pageSize'])
            || intval($params['pageSize']) < 1
        ) {
            $pageSize = 6;
        } else {
            $pageSize = (int) $params['pageSize'];
        }
        if (!isset($params['pageNumber'])
            || empty($params['pageNumber'])
            || intval($params['pageNumber']) < 1) {
            $pageNumber = 1;
        } else {
            $pageNumber = $params['pageNumber'] + 1;
        }
        if (isset($params['phrase']) && !empty($params['phrase'])) {
            $productCollection->addFieldToFilter('name',
                ['like' => '%'.$params['phrase'].'%']
            );
        }
        $productCollection->addStoreFilter();
        $productCollection->addFieldToFilter('status', array('eq' => '1'));
        $productCollection->addAttributeToFilter('price', array('gt' => 0));
        if (!empty($params['filters'])) {
            foreach ($params['filters'] as $f) {
                if (isset($f->selected)) {
                    $productCollection->addAttributeToSelect($f->id)
                        ->addAttributeToFilter($f->id, $f->selected->id);
                }
            }
        }
        if (!empty($params['sort'])) {
            $sortArr = explode('-', $params['sort']);
            $productCollection->addAttributeToSort($sortArr[0], $sortArr[1]);
        }
        $productCollection->setPage($pageNumber, $pageSize);
        $products = array();
        foreach ($productCollection as $product) {
            $this->setProduct($product);
            $products[] = $this->getProduct($product->getId());
        }
        $totalResultCount = $productCollection->getSize();
        $result = $this->fillProductSearch($totalResultCount,
            $pageNumber,
            $pageSize,
            $products,
            array(),
            $this->fillShortList());
        return $result;
    }
}
