<?php

/**
 * @author   dzgok  <dgokdunek@tmobtech.com>
 * @license  https://raw.githubusercontent.com/tappz/magento2/master/LICENCE
 *
 * @link     http://t-appz.com/
 */

namespace TmobLabs\Tappz\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class RequestHandler.
 */
class RequestHandler extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfig;
    public $request;

    /**
     * RequestHandler constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $httpRequest
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $httpRequest;
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        $server = $this->request->getServerValue();
        return $server['REQUEST_METHOD'];
    }

    /**
     * @return array
     */
    public function checkAuth()
    {
        $server = $this->request->getServerValue();

        if (!isset($server['HTTP_AUTHORIZATION']) ||
            empty($server['HTTP_AUTHORIZATION'])
        ) {
            $error = "Couldn't find AUTHORIZATION !Please "
                . "check your  .htaccess  ";
            throw new
            \Magento\Framework\Exception\AuthenticationException(__($error));
        }

        $header = (isset($server['HTTP_AUTHORIZATION'])
            && $server['HTTP_AUTHORIZATION'] != '')
            ? $server['HTTP_AUTHORIZATION'] : '';
        $auth = (explode(' ', $header));
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $baseUrl = $objectManager
            ->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()
            ->getBaseUrl();
        $url = substr($baseUrl, 0, -1);
        $realUrl = $this->getRealUrl($url);
        $username =
            $this->scopeConfig->
            getValue('tappztoken/tappzusermethod/tappzusername');
        $token = $this->scopeConfig->
        getValue('tappztoken/tappzusermethod/tappzsecretkey');
        $newToken = trim($token . '|' . ($realUrl) . '|' . $auth[2]);
        if (count($token) == 0) {
            $error = ' 401 - Token not initialized.Please'
                . 'create  token on configuration page ';
            throw new
            \Magento\Framework\Exception\AuthenticationException(__($error));
        } else if (
            sha1(($newToken), false) != $auth[1] || $username != $auth[0]
        ) {
            $er = __(' 403 - Access denied.Please check your tokens');
            throw new \Magento\Framework\Exception\AuthenticationException($er);
        }

        return $auth;
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function getRealUrl($url)
    {
        $server = $this->request->getServerValue();

        return urldecode($url . $server['REQUEST_URI']);
    }

    /**
     * @return array|string
     */
    public function getHeaderJson()
    {
        $resource = fopen('php://input', 'r');
        $result = [];
        while ($putData = fread($resource, 8192)) {
            $result = $putData;
        }
        fclose($resource);

        return $result;
    }

    /**
     * @return string
     */
    public function getAuthorizationFull()
    {
        $server = $this->request->getServerValue();
        $authorization = $server['HTTP_AUTHORIZATION'];
        $header = (isset($authorization) && $authorization != '') ?
            $authorization : '';

        return $header;
    }

    /**
     * @return mixed
     */
    public function getAuthorization()
    {
        $server = $this->request->getServerValue();

        if (!isset($server['HTTP_AUTHORIZATION']) ||
            empty($server['HTTP_AUTHORIZATION'])
        ) {
            $error = "Couldn't find AUTHORIZATION !Please "
                . "check your  .htaccess  ";
            throw new
            \Magento\Framework\Exception\AuthenticationException(__($error));
        }
        $authorization = $server['HTTP_AUTHORIZATION'];
        $header = (isset($authorization) && $authorization != '') ?
            $authorization : '';
        $auth = (explode(' ', $header));
        $result = (int)end($auth);

        return $result;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function convertJson($data)
    {
        return json_decode($data);
    }

    /**
     * @param $array
     * @param $oldKey
     * @param $newKey
     *
     * @return array
     */
    public function changeKey($array, $oldKey, $newKey)
    {
        if (!array_key_exists($oldKey, $array)) {
            return $array;
        }
        $keys = array_keys($array);
        $keys[array_search($oldKey, $keys)] = $newKey;

        return array_combine($keys, $array);
    }
}