<?php

namespace PiedWeb\Curl;

class Helper
{
    /**
     * Return scheme from proxy string and remove Scheme From proxy.
     *
     * @param string $proxy
     *
     * @return string
     */
    public static function getSchemeFrom(&$proxy)
    {
        if (!preg_match('@^([a-z0-9]*)://@', $proxy, $match)) {
            return 'http://';
        }
        $scheme = $match[1].'://';
        $proxy = str_replace($scheme, '', $proxy);

        return $scheme;
    }

    /**
     * Parse HTTP headers (php HTTP functions but generally, this packet isn't installed).
     *
     * @source http://www.php.net/manual/en/function.http-parse-headers.php#112917
     *
     * @param string $raw_headers Contain HTTP headers
     *
     * @return bool|array an array on success or FALSE on failure
     */
    public static function httpParseHeaders($raw_headers)
    {
        if (function_exists('http_parse_headers')) {
            http_parse_headers($raw_headers);
        }
        $headers = [];
        $key = '';
        foreach (explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);
            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], [trim($h[1])]);
                } else {
                    $headers[$h[0]] = array_merge([$headers[$h[0]]], [trim($h[1])]);
                }
                $key = $h[0];
            } else {
                if ("\t" == substr($h[0], 0, 1)) {
                    $headers[$key] .= "\r\n\t".trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
                trim($h[0]);
            }
        }

        return $headers;
    }

    public static function checkContentType($line, $expected = 'text/html')
    {
        return 0 === stripos(trim($line), 'content-type') && false !== stripos($line, $expected);
    }

    public static function checkStatusCode($line, $expected = 200)
    {
        return 0 === stripos(trim($line), 'http') && false !== stripos($line, ' '.$expected.' ');
    }
}
