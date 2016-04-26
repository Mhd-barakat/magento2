<?php

namespace TmobLabs\Tappz\Controller\Api;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context as Context;
use Magento\Framework\Controller\Result\JsonFactory as JSON;
use TmobLabs\Tappz\API\BasketRepositoryInterface;

class MergeBasket extends Action {

    protected $jsonResult;
    private $basketRepository;

    public function __construct(Context $context, JSON $json, BasketRepositoryInterface $basketRepository) {
        parent::__construct($context);
        $this->jsonResult = $json->create();
        $this->basketRepository = $basketRepository;
    }

    public function execute() {
    
      
        $result = array ();
        $this->basketRepository->merge();
        $this->jsonResult->setData($result);
        return $this->jsonResult;
    }

}
