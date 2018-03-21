<?php

namespace Kollway\Alipay;


class Utils
{
    
    protected static $instance;

    /**
     * HTTP 通讯类
     *
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * 排序参数
     *
     * @param array $params
     * @return array
     */
    public function sortParams(array $params)
    {
        ksort($params);
        reset($params);
        return $params;
    }

    /**
     * 过滤签名参数和空参数
     *
     * @param array $params
     * @return array
     */
    public function filterParams(array $params)
    {
        unset($params['sign']);
//        unset($params['sign_type']);
        return array_filter($params);
    }

    /**
     * 根据参数生成url
     *
     * @param array $params
     * @param bool $encoded
     * @return string
     */
    public function createParamUrl(array $params, $encoded = false)
    {
        $combinedParams = [];
        foreach ($params as $key => $val) {
            $combinedParams[] = implode('=', [$key, $encoded ? urlencode($val) : $val]);
        }
        $url = implode('&', $combinedParams);
        return $url;
    }

    /**
     * 获取HTTP通讯实例
     *
     * @return HttpClientInterface
     */
    public function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new CurlHttpClient();
        }
        return $this->httpClient;
    }

    /**
     * 设置HTTP通讯实例
     *
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * 获取工具类实例
     *
     * @return Utils
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
}