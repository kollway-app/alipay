<?php


namespace Kollway\Alipay;


use Kollway\Alipay\Exception\AlipayException;
use Kollway\Alipay\Pay\PayComposer;
use Kollway\Alipay\Signer\SignerInterface;
use Kollway\Alipay\Verifier\Verifier;

class Alipay
{

    /**
     * 应用ID '2018012402059482'
     *
     * @var string
     */
    protected $app_id;

    /**
     * 合作伙伴 ID '2000202202020'
     *
     * @var string
     */
    protected $partner;

    /**
     * 签名类
     *
     * @var Signer\SignerInterface[]
     */
    protected $signers = [];

    /**
     * The default sign type for the payment to be composed.
     *
     * @var string
     */
    protected $defaultSignType;

    protected $params;

    /**
     * 新建实例
     *
     * @param string $partner
     */
    public function __construct($app_id, $partner) {
        $this->app_id = $app_id;
        $this->partner = $partner;
        $this->params = array();
    }

    /**
     * Add a signer.
     *
     * @param SignerInterface $signer
     * @return $this
     */
    public function addSigner(SignerInterface $signer)
    {
        $this->signers[$signer->getSignType()] = $signer;
        return $this;
    }

    /**
     * Get the signer by signer type.
     *
     * @param string $signType
     * @return SignerInterface
     * @throws AlipayException
     */
    public function getSigner($signType)
    {
        if (isset($this->signers[$signType])) {
            return $this->signers[$signType];
        }
        throw new AlipayException(sprintf('Signer type [%s] not found.', $signType));
    }

    /**
     * Set the default sign type.
     *
     * @param string $signType
     * @return $this
     */
    public function setDefaultSignType($signType)
    {
        $this->defaultSignType = $signType;
        return $this;
    }

    /**
     * Get the default sign type.
     *
     * @return string
     */
    public function getDefaultSignType()
    {
        return $this->defaultSignType;
    }

    /**
     * 获取应用ID
     *
     * @return string
     */
    public function getAppId() {
        return $this->app_id;
    }

    /**
     * 获取合作伙伴ID
     *
     * @return string
     */
    public function getPartner()
    {
        return $this->partner;
    }

    public function getParams() {
        return $this->params;
    }

    public function setParams($params) {
        $this->params = $params;
    }

    public function putParam($key, $value) {
        if($this->params) {
            $this->params[$key] = $value;
        }
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
        $params = array(
            'service' =>'create_direct_pay_by_user',
            'payment_type' =>'1',
            '_input_charset' =>'utf-8',
            'notify_url' => $notifyUrl,
            'return_url' => $returnUrl,
            'out_trade_no' => $outTradeNo,
            'subject' => $subject,
            'total_fee' => $fee,
        );
        $pay = new PayComposer($this, 'https://mapi.alipay.com/gateway.do?');
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
        $params = array(
            'service' =>'alipay.wap.create.direct.pay.by.user',
            'payment_type' =>'1',
            '_input_charset' =>'utf-8',
            'notify_url' => $notifyUrl,
            'return_url' => $returnUrl,
            'out_trade_no' => $outTradeNo,
            'subject' => $subject,
            'total_fee' => $fee,
        );
        $pay = new PayComposer($this, 'https://mapi.alipay.com/gateway.do?');
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
        $params['method'] = 'alipay.trade.app.pay';
        $params['return_url'] = 'm.alipay.com';
        $params['notify_url'] = $notifyUrl;
        $params['biz_content'] = json_encode($biz_content);

        $pay = new PayComposer($this);
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
     * @param string $partner
     * @param array $signers
     * @param string $defaultSignType
     * @return static
     */
    public static function create($app_id, $partner, $signers = [], $defaultSignType = SignerInterface::TYPE_RSA2)
    {
        $alipay = new static($app_id, $partner);
        $alipay->defaultSignType = $defaultSignType;
        foreach ((array)$signers as $signer) {
            $alipay->addSigner($signer);
        }
        return $alipay;
    }

}