<?php

/**
 * @author   dzgok  <dgokdunek@tmobtech.com>
 * @license  https://raw.githubusercontent.com/tappz/magento2/master/LICENCE
 *
 * @link     http://t-appz.com/
 */

namespace TmobLabs\Tappz\Controller\Api;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context as Context;
use Magento\Framework\Controller\Result\JsonFactory as JSON;
use TmobLabs\Tappz\API\IndexRepositoryInterface;
use TmobLabs\Tappz\Helper\RequestHandler as RequestHandler;

/**
 * Class Index.
 */
class Index extends Action
{
    /**
     * @var
     */
    private $jsonResult;
    /**
     * @var IndexRepositoryInterface
     */
    private $indexRepository;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param JSON $json
     * @param IndexRepositoryInterface $indexRepository
     * @param RequestHandler $helper
     */
    public function __construct(
        Context $context,
        JSON $json,
        IndexRepositoryInterface $indexRepository,
        RequestHandler $helper
    ) {
        parent::__construct($context);
        $this->jsonResult = $json->create();
        $this->indexRepository = $indexRepository;
        $helper->checkAuth();
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $result = $this->indexRepository->getIndex();
        $this->jsonResult->setData($result);

        return $this->jsonResult;
    }
}
