<?php

/**
 * @author   dzgok  <dgokdunek@tmobtech.com>
 * @license  https://raw.githubusercontent.com/tappz/magento2/master/LICENCE
 *
 * @link     http://t-appz.com/
 */

namespace TmobLabs\Tappz\Model\Basket;

use TmobLabs\Tappz\API\BasketRepositoryInterface;
use TmobLabs\Tappz\Model\Purchase\PurchaseCollector;

/**
 * Class BasketRepository.
 */
class BasketRepository implements BasketRepositoryInterface
{
    /**
     * @var BasketCollector
     */
    private $basketCollector;
    /**
     * @var PurchaseCollector
     */
    private $purchaseCollector;

    /**
     * BasketRepository constructor.
     *
     * @param BasketCollector $basketCollector
     * @param PurchaseCollector $purchaseCollector
     */
    public function __construct(
        BasketCollector $basketCollector,
        PurchaseCollector $purchaseCollector
    ) {
        $this->basketCollector = $basketCollector;
        $this->purchaseCollector = $purchaseCollector;
    }

    /**
     * @param $basketId
     *
     * @return array
     */
    public function getByBasketById($basketId)
    {
        $result = $this->basketCollector->getBasketById($basketId);

        return $result;
    }

    /**
     * @return array
     */
    public function getUserBasket()
    {
        $result = $this->basketCollector->getUserBasket();

        return $result;
    }

    /**
     * @param $quoteId
     *
     * @return array
     */
    public function getPayment($quoteId)
    {
        $result = $this->basketCollector->getBasketPayment($quoteId);

        return $result;
    }

    /**
     * @param null $quoteId
     *
     * @return array
     */
    public function getLines($quoteId = null)
    {
        $result = $this->basketCollector->getLines($quoteId);

        return $result;
    }

    /**
     * @param null $quoteId
     *
     * @return array
     */
    public function getAddress($quoteId = null)
    {
        $result = $this->basketCollector->setAddress($quoteId);

        return $result;
    }

    /**
     * @param null $quoteId
     *
     * @return array
     */
    public function getContract()
    {
        $result = $this->basketCollector->getBasketContract();

        return $result;
    }

    /**
     * @param $quoteId
     * @param $method
     *
     * @return array|void
     */
    public function getPurchase($quoteId, $method)
    {
        $result = $this->purchaseCollector->getPurchase($quoteId, $method);

        return $result;
    }

    /**
     * @return array
     */
    public function merge()
    {
        $result = $this->basketCollector->merge();

        return $result;
    }
}
