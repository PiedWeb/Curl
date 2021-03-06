<?php

declare(strict_types=1);

namespace PiedWeb\Curl\Test;

use PiedWeb\Curl\MultipleCheckInHeaders;
use PiedWeb\Curl\Request;
use PiedWeb\Curl\Response;
use PiedWeb\Curl\ResponseFromCache;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    public function testDownloadIfHtml()
    {
        $url = 'https://piedweb.com/';
        $request = new Request($url);
        $request
            ->setDefaultGetOptions()
            ->setDownloadOnlyIf(function ($line) {
                return 0 === stripos(trim($line), 'content-type') && false !== stripos($line, 'text/html');
            })
            ->setReturnHeader()
            ->setDesktopUserAgent()
            ->setEncodingGzip()
        ;
        $result = $request->exec();

        $this->assertSame(200, $result->getStatusCode());

        $headers = $result->getHeaders();
        $this->assertTrue(is_array($headers));

        $this->assertSame('text/html; charset=UTF-8', $result->getContentType());
        $this->assertTrue(strlen($result->getContent()) > 10);
    }

    public function testNotDownload()
    {
        $url = 'https://piedweb.com/assets/img/xl/bg.jpg';
        $request = new Request($url);
        $request
            ->setDefaultGetOptions()
            ->setDownloadOnlyIf('PiedWeb\Curl\Helper::checkContentType')
            ->setReturnHeader()
            ->setDesktopUserAgent()
            ->setEncodingGzip()
        ;
        $result = $request->exec();

        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('', $result->getContent());
    }

    public function testEffectiveUrl()
    {
        $url = 'http://www.piedweb.com/';
        $request = new Request($url);
        $request
            ->setDefaultGetOptions()
            ->setDownloadOnlyIf('PiedWeb\Curl\Helper::checkContentType')
            ->setReturnHeader()
            ->setDesktopUserAgent()
            ->setEncodingGzip()
        ;
        $result = $request->exec();

        $this->assertSame('https://piedweb.com/', $result->getEffectiveUrl());
        $this->assertSame($url, $result->getUrl());
        $this->assertTrue(strlen($result->getContent()) > 10);
    }

    public function testCurlError()
    {
        $url = 'http://www.readze'.rand(100000, 99999999).'.com/';
        $request = new Request($url);
        $request
            ->setDefaultGetOptions()
            ->setReturnHeader()
            ->setDesktopUserAgent()
            ->setEncodingGzip()
        ;
        $result = $request->exec();

        $this->assertSame(6, $result);
    }

    public function test404()
    {
        $url = 'https://piedweb.com/404-error';
        $request = new Request($url);
        $request
            ->setDefaultGetOptions()
            ->setDownloadOnlyIf('PiedWeb\Curl\Helper::checkContentType')
            ->setDesktopUserAgent()
            ->setEncodingGzip()
        ;
        $result = $request->exec();

        $this->assertSame(404, $result->getStatusCode());
    }

    public function testAllMethods()
    {
        $checkHeaders = new MultipleCheckInHeaders();

        $url = 'https://piedweb.com';
        $request = new Request($url);
        $request
            ->setDefaultGetOptions()
            ->setDefaultSpeedOptions()
            ->setCookie('hello=1')
            ->setReferer('https://piedweb.com')
            ->setUserAgent('Hello :)')
            ->setDesktopUserAgent()
            ->setMobileUserAgent()
            ->setLessJsUserAgent()
            ->setUrl($url)
            ->setReturnHeader()
            ->setDownloadOnlyIf([$checkHeaders, 'check'])
        ;

        $result = $request->exec();

        $this->assertSame($result->getRequest()->getUrl(), $url);
        $this->assertSame($result->getRequest()->mustReturnHeaders(), Request::RETURN_HEADER);
        $this->assertSame($result->getRequest()->getUserAgent(), $request->lessJsUserAgent);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('text/html', $result->getMimeType());

        $headers = $result->getHeaders();
        $this->assertTrue(is_array($headers));

        $this->assertSame('text/html; charset=UTF-8', $result->getContentType());

        $this->assertTrue(strlen($result->getContent()) > 100);
    }

    public function testMultipleCheckInHeaders()
    {
        $checkHeaders = new MultipleCheckInHeaders();

        $url = 'https://piedweb.com/404-error';
        $request = new Request($url);
        $request
            ->setDefaultGetOptions()
            ->setDefaultSpeedOptions()
            ->setUserAgent('Hello :)')
            ->setDownloadOnlyIf([$checkHeaders, 'check'])
            ->setReturnHeader(true)
            ->setPost('testpost')
        ;

        $result = $request->exec();

        if (is_int($result)) {
            var_dump($result);
        } else {
            $this->assertTrue(is_array($result->getHeaders()));
            $this->assertSame(null, $result->getCookies());
            $this->assertSame(404, $result->getInfo('http_code'));
            $this->assertSame(0, $result->getRequest()->hasError());
            $this->assertSame(404, $result->getRequest()->getInfo(CURLINFO_HTTP_CODE));
            $result->getRequest()->close();
        }
    }

    public function testProxy()
    {
        $url = 'https://piedweb.com/404-error';
        $request = new Request($url);
        $request
            ->setProxy('75.157.242.104:59190')
            ->setNoFollowRedirection()
        ;

        $result = $request->exec();

        $this->assertTrue(is_int($result));
        $this->assertStringContainsString('Failed to connect', $request->getError());
    }

    public function testAbortIfTooBig()
    {
        $url = 'https://piedweb.com';
        $request = new Request($url);
        $request->setAbortIfTooBig(1);

        $result = $request->exec();
        $this->assertSame($result, 42);
    }

    public function testDownloadOnlyFirstBytes()
    {
        $url = 'https://piedweb.com';
        $request = new Request($url);
        $request->setDownloadOnly('0-199');

        $result = $request->exec();

        $this->assertTrue(strlen($result->getContent()) < 300);
    }

    public function testResponseFromCache()
    {
        $response = new ResponseFromCache(
            'HTTP/1.1 200 OK'.PHP_EOL.PHP_EOL.'<!DOCTYPE html><html><body><p>Tests</p></body>',
            'https://piedweb.com/',
            ['content_type' => 'text/html; charset=UTF-8']
        );

        $this->assertTrue($response instanceof Response);
        $this->assertSame($response->getRequest(), null);
        $this->assertSame($response->getMimeType(), 'text/html');
        $this->assertSame($response->getContent(), '<!DOCTYPE html><html><body><p>Tests</p></body>');
    }
}
