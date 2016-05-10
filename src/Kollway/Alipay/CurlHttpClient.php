<?php

namespace Foccy\Alipay;


use Foccy\Alipay\Exception\HttpException;

class CurlHttpClient implements HttpClientInterface
{

    /**
     * Make request.
     *
     * @param string $url
     * @param string $method
     * @param array $data
     * @return mixed
     * @throws Exception\HttpException
     */
    public function executeHttpRequest($url, $method = self::METHOD_GET, array $data = [])
    {
        $ch = curl_init();
        if ($method === self::METHOD_GET) {
            if ($data) {
                $query = $this->buildQuery($data);
                if (strpos($url, '?') !== false) {
                    $url .= $query;
                } else {
                    $url .= '?' . $query;
                }
            }
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $body = curl_exec($ch);
        $info = curl_getinfo($ch);
        $code = $info['http_code'];
        if ($code === 200) {
            return $body;
        } else {
            $error = curl_errno($ch);
            $msg = curl_error($ch);
            throw new HttpException($code, sprintf('%s %s', $error, $msg));
        }
    }

    /**
     * Build query string.
     *
     * @param array $params
     * @return string
     */
    protected function buildQuery(array $params)
    {
        $segments = [];
        foreach ($params as $key => $value) {
            $segments[] = $key . '=' . urlencode($value);
        }
        return implode('&', $segments);
    }

}