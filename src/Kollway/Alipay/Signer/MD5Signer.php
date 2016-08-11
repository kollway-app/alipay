<?php

namespace Kollway\Alipay\Signer;


class MD5Signer implements SignerInterface
{

    /**
     * The signing key.
     *
     * @var string
     */
    protected $key;

    /**
     * Create a new instance.
     *
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Sign the raw data.
     *
     * @param $paramString
     * @return string
     */
    public function sign($paramString)
    {
        return md5($paramString . $this->key);
    }

    /**
     * Verify the raw data.
     *
     * @param string $paramString
     * @param string $sign
     * @return bool
     */
    public function verify($paramString, $sign)
    {
        $alipaySign = md5($paramString . $this->key);
        return $alipaySign === $sign;
    }

    /**
     * Get the sign type name.
     *
     * @return string
     */
    public function getSignType()
    {
        return self::TYPE_MD5;
    }

}