<p align="center"><a href="https://dev.piedweb.com">
<img src="https://raw.githubusercontent.com/PiedWeb/piedweb-devoluix-theme/master/src/img/logo_title.png" width="200" height="200" alt="Open Source Package" />
</a></p>

# Curl OOP Wrapper

[![Latest Version](https://img.shields.io/github/tag/PiedWeb/Curl.svg?style=flat&label=release)](https://github.com/PiedWeb/Curl/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/PiedWeb/Curl/Tests?label=tests)](https://github.com/PiedWeb/Curl/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/PiedWeb/Curl.svg?style=flat)](https://scrutinizer-ci.com/g/PiedWeb/Curl)
[![Code Coverage](https://codecov.io/gh/PiedWeb/Curl/branch/master/graph/badge.svg)](https://codecov.io/gh/PiedWeb/Curl/branch/master)
[![Type Coverage](https://shepherd.dev/github/PiedWeb/Curl/coverage.svg)](https://shepherd.dev/github/PiedWeb/Curl)
[![Total Downloads](https://img.shields.io/packagist/dt/piedweb/curl.svg?style=flat)](https://packagist.org/packages/piedweb/curl)

Simple PHP Curl OOP wrapper for efficient request.

For a more complex or abstracted curl wrapper, use [Guzzle](https://guzzle.readthedocs.io/en/latest/).

## Install

Via [Packagist](https://img.shields.io/packagist/dt/piedweb/curl.svg?style=flat)

``` bash
$ composer require piedweb/curl
```

## Usage

Quick Example :
``` php
$url = 'https://piedweb.com';
$request = new Request($url);
$request
    ->setDefaultSpeedOptions(true)
    ->setDownloadOnlyIf('PiedWeb\Curl\Helper::checkContentType') // 'PiedWeb\Curl\Helper::checkStatusCode'
    ->setDesktopUserAgent()
;
$result = $request->exec();
if ($result instanceof \PiedWeb\Curl\Response) {
    $content = $this->getContent();
}
```

Static Wrapper Methods :
``` php
use PiedWeb\Curl\Request;

Request::get($url); // @return string

```

All Other Methods :
``` php
use PiedWeb\Curl\Request;

$r = new CurlRequest(?string $url);
$r
    ->setOpt(CURLOPT_*, mixed 'value')

	// Preselect Options to avoid eternity wait
    ->setDefaultGetOptions($connectTimeOut = 5, $timeOut = 10, $dnsCacheTimeOut = 600, $followLocation = true, $maxRedirs = 5)
    ->setDefaultSpeedOptions() // no header except if setted, 1 redir max, no ssl check
    ->setNoFollowRedirection()

    ->setReturnHeader($only = false)
        ->mustReturnHeaders() // @return int corresponding to Request::RETURN_HEADER_ONLY or Request::RETURN_HEADER or NULL
    ->setCookie(string $cookie)
    ->setReferer(string $url)

    ->setUserAgent(string $ua)
    ->setDesktopUserAgent()
    ->setMobileUserAgent()
    ->setLessJsUserAgent()
        ->getUserAgent() // @return string

    ->setDownloadOnlyIf(callable $func) // @param $ContentType can be a String or an Array
    ->setAbortIfTooBig(int $tooBig = 200000) // @defaut 2Mo
    ->setDownloadOnly($range = '0-500')

    ->setPost(array $post)

    ->setEncodingGzip()

    ->setProxy(string '[scheme]proxy-host:port[:username:passwrd]') // Scheme, username and passwrd are facultatives. Default Scheme is http://

    ->setUrl($url)
        ->getUrl()

$response = $r->exec(); // @return PiedWeb\Curl\Response or int corresponding to the curl error

$response->getUrl(); // @return string
$response->getContentType(); // @return string
$response->getContent(); // @return string
$response->getHeaders($returnArray = true); // @return array Response Header (or in a string if $returnArray is set to false)
$response->getCookies(); // @return string
$response->getEffectiveUrl(); // @return string

$r->hasError(); // Equivalent to curl function curl_errno
$r->getError(); // .. curl_error
$r->getInfo(?string $key = null); // ... curl_getinfo or getting directly the $key value


use PiedWeb\Curl\ResponseFromCache;

$response = new ResponseFromCache(  // same methods than Response except getRequest return null
    string $filePathOrContent,
    ?string $url = null,
    array $info = [],
    $headers = PHP_EOL.PHP_EOL
);

```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing](https://dev.piedweb.com/contributing)

## Credits

- [PiedWeb](https://piedweb.com)
- [All Contributors](https://github.com/PiedWeb/:package_skake/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
