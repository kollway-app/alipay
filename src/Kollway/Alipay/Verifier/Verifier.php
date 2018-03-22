<?php

namespace Kollway\Alipay\Verifier;


use Kollway\Alipay\Alipay;
use Kollway\Alipay\Utils;

class Verifier {

    /**
     * The Alipay instance.
     *
     * @var Alipay
     */
    protected $alipay;

    /**
     * The parameters to be validated.
     *
     * @var array
     */
    protected $params;

    // 表单提交字符集编码
    public $postCharset = "UTF-8";
    private $fileCharset = "UTF-8";
    
    /**
     * Create a new instance.
     *
     * @param Alipay $alipay
     * @param array $params
     */
    public function __construct(Alipay $alipay, array $params)
    {
        $this->alipay = $alipay;
        $this->params = $params;
    }

    public function verify($alipay_version='v1') {
        $params = $this->params;
        $sign = $params['sign'];

        if($alipay_version == 'v1') {
            unset($params['sign_type']);
        }

        $utils = Utils::getInstance();
        $params = $utils->sortParams($params);
        $params = $utils->filterParams($params);
        $is_valid = $this->alipay->getSigner()->verify($utils->createParamUrl($params), $sign);

        return $is_valid;
    }

    /**
     * Get the verified parameter.
     *
     * @param $name
     * @return string|null
     */
    public function getParam($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    /**
     * Check if a parameter exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasParam($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * Get all parameters.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

}