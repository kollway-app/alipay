# 签名方式

md5 签名方式
```php
use Foccy\Alipay\Signer\MD5Signer;
$md5Signer = new MD5Signer('a2d9bm4jfk0slemvpaq23');
$alipay->addSigner($md5Signer);
```

rsa 签名方式
```php
use Foccy\Alipay\Signer\RSASigner;
$rsaSigner = new RSASigner(__DIR__ . '/my_private_key', __DIR__ . '/alipay_pub_key');
$alipay->addSigner($rsaSigner);
```

生成 Alipay 实例
```php
use Foccy\Alipay\Alipay;
use Foccy\Alipay\Signer\SignerInterface;

// 生成 Alipay 实例，签名方式包括`md5`和`rsa`，默认`md5`方式签名
$alipay = Alipay::create('2008...', [$md5Signer, $rsaSigner], SignerInterface::TYPE_MD5);
```

### PC网页支付
对应支付宝接口服务名 `create_direct_pay_by_user`
```php
$webPay = $alipay->createWebPay('out_order_number', 'product_name', '0.01', 'http://www.example.com/payNotify', 'http://www.example.com/payReturn');

// 生成支付宝支付URL
echo $webPay->compose(); // 生成跳转到支付宝的URL

// 设置网银支付银行
$webPay->set('defaultbank', 'CMB');
echo $webPay->compose();

// 以RSA签名方式生成支付URL
echo $webPay->compose(SignerInterface::TYPE_RSA);
```

### WAP网页支付
对应支付宝接口服务名 `alipay.wap.create.direct.pay.by.user`
```php
$wapPay = $alipay->createWapPay('out_order_number', 'product_name', '0.01', 'http://www.example.com/payNotify', 'http://www.example.com/payReturn');

echo $wapPay->compose(); // 生成支付宝支付URL
```

### 手机客户端支付
对应支付宝接口服务名 `mobile.securitypay.pay`
```php
$mobilePay = $alipay->createMobilePay('out_order_number', 'foo', 'body', '0.01', 'http://www.exmaple.com/payNotify');

// 手机客户端只支持 RSA 签名
echo $mobilePay->compose(SignerInterface::TYPE_RSA);
```

### 验证 return 或 notify 通知
```php
$parameters = $_POST; // $parameters = $_GET;
$verifier = $alipay->createVerifier($parameters);
if ($verifier->verify()) {
    switch ($verifier->getParam('trade_status')) {
        case 'TRADE_FINISHED':
        case 'TRADE_SUCCESS':
            // 支付成功, 处理业务逻辑
            break;
        default:
            // 未能支付成功
            break;
    }
    // 通过验证
} else {
    // 验证失败
}
```