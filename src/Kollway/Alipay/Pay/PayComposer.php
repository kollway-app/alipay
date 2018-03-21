<?php

namespace Kollway\Alipay\Pay;


use Kollway\Alipay\Alipay;
use Kollway\Alipay\Signer\SignerInterface;
use Kollway\Alipay\Utils;

class PayComposer
{

    /**
     * The Alipay instance.
     *
     * @var Alipay
     */
    protected $alipay;

    /**
     * The parameters required for the payment.
     *
     * @var array
     */
    protected $params = [];

    /**
     * The gateway url for alipay payment.
     *
     * @var string
     */
    protected $gatewayUrl;

    /**
     * Create a new instance.
     *
     * @param Alipay $alipay
     * @param string $gatewayUrl
     */
    public function __construct(Alipay $alipay, $gatewayUrl = 'https://openapi.alipay.com/gateway.do') {
        $this->alipay = $alipay;
        $this->gatewayUrl = $gatewayUrl;

        $this->params = array(
            'app_id' => $alipay->getAppId(),
            'partner' => $alipay->getPartner(),
            'biz_content' => '',
            'charset' => 'utf-8',
            'timestamp' => date('Y-m-d H:i:s', time()),
            'version' => '1.0',
        );
    }

    /**
     * Create a payment url.
     *
     * @param null|string $signType
     * @return string string
     */
    public function compose($signType = null)
    {
        if (is_null($signType)) {
            $signType = $this->alipay->getDefaultSignType();
        }
        $params = $this->params;
        $params['sign_type'] = $signType;
        $utils = Utils::getInstance();
        $params = $utils->sortParams($params);
        $params = $utils->filterParams($params);
        $sign = $this->alipay->getSigner($signType)->sign($utils->createParamUrl($params));
        $params['sign'] = $sign;
        return $this->gatewayUrl . $utils->createParamUrl($params, true);
    }

    /**
     * Set a payment parameter.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Remove a payment parameter.
     *
     * @param string $name
     * @return $this
     */
    public function remove($name)
    {
        unset($this->params[$name]);
        return $this;
    }

    /**
     * Add multiple parameters.
     *
     * @param array $params
     */
    public function add(array $params)
    {
        $this->params = array_merge($this->params, $params);
    }

}