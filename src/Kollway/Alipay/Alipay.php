<?php


namespace Kollway\Alipay;


use Kollway\Alipay\Pay\PayComposer;
use Kollway\Alipay\Signer\SignerInterface;
use Kollway\Alipay\Verifier\Verifier;

class Alipay
{

    const DEFAULT_GATEWAY = 'https://openapi.alipay.com/gateway.do';

    /**
     * 应用ID '2018012402059482'
     *
     * @var string
     */
    protected $app_id;


    /**
     * 签名类
     *
     * @var Signer\SignerInterface
     */
    protected $signer;

    protected $params;
    protected $biz_params;

    /**
     * 新建实例
     *
     * @param string $partner
     */
    private function __construct($app_id) {
        $this->app_id = $app_id;
        $this->params = array();
        $this->biz_params = array();
    }

    /**
     * Get the signer by signer type.
     *
     * @return SignerInterface
     */
    public function getSigner() {
        return $this->signer;
    }

    /**
     * 获取应用ID
     *
     * @return string
     */
    public function getAppId() {
        return $this->app_id;
    }

    public function getParams() {
        return $this->params;
    }

    public function setParams($params) {
        $this->params = $params;
    }

    public function putParam($key, $value) {
        $this->params[$key] = $value;
    }

    public function putBizParam($key, $value) {
        $this->biz_params[$key] = $value;
    }

    /**
     * PC网页支付
     *
     * @param string $outTradeNo
     * @param string $subject
     * @param string $fee
     * @param string $notifyUrl
     * @param string $returnUrl
     * @return PayComposer
     */
    public function createWebPay($outTradeNo, $subject, $fee, $notifyUrl, $returnUrl)
    {
        $params = $this->getParams();
        $biz_content = array(
            'timeout_express' => '30m',
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
            'out_trade_no' => $outTradeNo,
            'total_amount' => $fee,
            'subject' => $subject,
            'body' => '',
        );
        $biz_content = array_merge($biz_content, $this->biz_params);

        $params['method'] = 'alipay.trade.page.pay';
        $params['return_url'] = $returnUrl;
        $params['notify_url'] = $notifyUrl;
        $params['biz_content'] = json_encode($biz_content);

        $pay = new PayComposer($this, self::DEFAULT_GATEWAY);
        $pay->add($params);
        return $pay;
    }

    /**
     * Wap支付
     *
     * @param string $outTradeNo
     * @param string $subject
     * @param string $fee
     * @param string $notifyUrl
     * @param string $returnUrl
     * @return PayComposer
     */
    public function createWapPay($outTradeNo, $subject, $fee, $notifyUrl, $returnUrl)
    {
        $params = $this->getParams();
        $biz_content = array(
            'timeout_express' => '30m',
            'product_code' => 'QUICK_WAP_WAY',
            'out_trade_no' => $outTradeNo,
            'total_amount' => $fee,
            'subject' => $subject,
            'body' => '',
        );
        $biz_content = array_merge($biz_content, $this->biz_params);

        $params['method'] = 'alipay.trade.wap.pay';
        $params['return_url'] = $returnUrl;
        $params['notify_url'] = $notifyUrl;
        $params['biz_content'] = json_encode($biz_content);

        $pay = new PayComposer($this, self::DEFAULT_GATEWAY);
        $pay->add($params);
        return $pay;
    }

    /**
     * 客户端支付
     *
     * @param string $outTradeNo
     * @param string $subject
     * @param string $body
     * @param string $fee
     * @param string $notifyUrl
     * @return PayComposer
     */
    public function createMobilePay($outTradeNo, $subject, $body, $fee, $notifyUrl)
    {
        $params = $this->getParams();
        $biz_content = array(
            'timeout_express' => '30m',
            'product_code' => 'QUICK_MSECURITY_PAY',
            'out_trade_no' => $outTradeNo,
            'total_amount' => $fee,
            'subject' => $subject,
            'body' => $body,
        );
        $biz_content = array_merge($biz_content, $this->biz_params);

        $params['method'] = 'alipay.trade.app.pay';
        $params['return_url'] = 'm.alipay.com';
        $params['notify_url'] = $notifyUrl;
        $params['biz_content'] = json_encode($biz_content);

        $pay = new PayComposer($this);
        $pay->add($params);
        return $pay;
    }


    public function createBarPay($outTradeNo, $subject, $body, $fee, $app_auth_token, $auth_code) {
        $params = $this->getParams();
        $biz_content = array(
            'timeout_express' => '30m',
            'product_code' => 'FACE_TO_FACE_PAYMENT',
            'scene' => 'bar_code',
            'auth_code' => $auth_code,
            'out_trade_no' => $outTradeNo,
            'total_amount' => $fee,
            'subject' => $subject,
            'body' => $body,
        );
        $biz_content = array_merge($biz_content, $this->biz_params);

        $params['method'] = 'alipay.trade.pay';
        $params['return_url'] = '';
        $params['notify_url'] = '';
        $params['app_auth_token'] = $app_auth_token;
        $params['biz_content'] = json_encode($biz_content);

        $pay = new PayComposer($this, self::DEFAULT_GATEWAY);
        $pay->add($params);
        return $pay;
    }

    /**
     * 生成支付通知验证实例
     *
     * @param array $params
     * @return Verifier
     */
    public function createVerifier(array $params)
    {
        return new Verifier($this, $params);
    }

    /**
     * 生成Alipay实例
     *
     * @param string $app_id
     * @param Signer\SignerInterface $signer
     * @return static
     */
    public static function create($app_id, $signer)
    {
        $alipay = new static($app_id);
        $alipay->signer = $signer;

        return $alipay;
    }

}