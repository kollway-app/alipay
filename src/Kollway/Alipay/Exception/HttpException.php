<?php

namespace Kollway\Alipay\Exception;


use Exception;

class HttpException extends AlipayException
{

    /**
     * The HTTP status code.
     *
     * @var int
     */
    protected $statusCode;

    /**
     * @param string $statusCode
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($statusCode, $message = "", $code = 0, Exception $previous = null)
    {
        $this->statusCode = (int)$statusCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the HTTP status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

}