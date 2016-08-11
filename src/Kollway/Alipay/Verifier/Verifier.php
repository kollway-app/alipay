<?php

namespace Kollway\Alipay\Verifier;


use Foccy\Alipay\Alipay;
use Foccy\Alipay\Utils;

class Verifier
{

    /**
     * The Alipay instance.
     *
     * @var Alipay
     */
    protected $alipay;

    /**
     * The alipay url to verify the notify id.
     *
     * @var string
     */
    protected $notifyServiceUrl = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';

    /**
     * Whether the parameters are validated.
     *
     * @var bool
     */
    protected $isVerified = false;

    /**
     * The parameters to be validated.
     *
     * @var array
     */
    protected $params;
    
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

    /**
     * Verify the notify or return data.
     *
     * @return bool
     */
    public function verify()
    {
        if ($this->isVerified) {
            return true;
        }
        $params = $this->params;
        if (empty($params) || !isset($params['sign']) || !isset($params['sign_type'])) {
            return false;
        }
        $utils = Utils::getInstance();
        $sign = $params['sign'];
        $signType = $params['sign_type'];
        $isVerified = $this->alipay->getSigner($signType)->verify($utils->createParamUrl($utils->sortParams($utils->filterParams($params))), $sign);
        if ($isVerified) {
            if (empty($params['notify_id'])) {
                $this->isVerified = true;
                return true;
            }
            $verify_url = $this->notifyServiceUrl . "partner=" . $this->alipay->getPartner() . "&notify_id=" . $params["notify_id"];
            $responseTxt = $utils->getHttpClient()->executeHttpRequest($verify_url);
            $this->isVerified = $responseTxt === 'true';
            return $this->isVerified;
        }
        return false;
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