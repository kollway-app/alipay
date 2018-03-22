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
    public function __construct(Alipay $alipay, $gatewayUrl = '') {
        $this->alipay = $alipay;
        $this->gatewayUrl = $gatewayUrl;

        $this->params = array(
            'app_id' => $alipay->getAppId(),
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
    public function compose() {
        $signer = $this->alipay->getSigner();
        $params = $this->params;
        $params['sign_type'] = $signer->getSignType();
        $utils = Utils::getInstance();
        $params = $utils->sortParams($params);
        $params = $utils->filterParams($params);
        $sign = $signer->sign($utils->createParamUrl($params));
        $params['sign'] = $sign;

        $url = $utils->createParamUrl($params, true);
        if($this->gatewayUrl) {
            $url = sprintf('%s?%s', $this->gatewayUrl, $url);
        }
        return $url;
    }

    public function composeHtmlForm() {
        $signer = $this->alipay->getSigner();
        $params = $this->params;
        $params['sign_type'] = $signer->getSignType();;
        $utils = Utils::getInstance();
        $params = $utils->sortParams($params);
        $params = $utils->filterParams($params);
        $sign = $signer->sign($utils->createParamUrl($params));
        $params['sign'] = $sign;

        $html = sprintf("<form id='alipaysubmit' name='alipaysubmit' action='%s' method='POST'>", $this->gatewayUrl);
        foreach ($params as $key => $val) {
            $val = str_replace("'","&apos;",$val);
            $html .= sprintf("<input type='hidden' name='%s' value='%s'/>", $key, $val);
        }

        $html .= "<input type='submit' value='ok' style='display:none;'></form>";
        $html .= "<script>document.forms['alipaysubmit'].submit();</script>";
        return $html;
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