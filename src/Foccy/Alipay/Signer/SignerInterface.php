<?php

namespace Foccy\Alipay\Signer;


interface SignerInterface
{

    const TYPE_MD5 = 'MD5';

    const TYPE_RSA = 'RSA';

    /**
     * Sign the raw data.
     *
     * @param $raw
     * @return string
     */
    public function sign($raw);

    /**
     * Verify the raw data.
     *
     * @param string $raw
     * @param string $sign
     * @return bool
     */
    public function verify($raw, $sign);

    /**
     * Get the sign type name.
     *
     * @return string
     */
    public function getSignType();

}