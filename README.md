Wandu Http
===

[![Latest Stable Version](https://poser.pugx.org/wandu/http/v/stable.svg)](https://packagist.org/packages/wandu/http)
[![Latest Unstable Version](https://poser.pugx.org/wandu/http/v/unstable.svg)](https://packagist.org/packages/wandu/http)
[![Total Downloads](https://poser.pugx.org/wandu/http/downloads.svg)](https://packagist.org/packages/wandu/http)
[![License](https://poser.pugx.org/wandu/http/license.svg)](https://packagist.org/packages/wandu/http)

[![Build Status](https://img.shields.io/travis/Wandu/Http/master.svg)](https://travis-ci.org/Wandu/Http)
[![Code Coverage](https://scrutinizer-ci.com/g/Wandu/Http/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Wandu/Http/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Wandu/Http/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Wandu/Http/?branch=master)

Http Psr-7(Based on 1.0.0) Implementation. More details: [www.php-fig.org/psr/psr-7](http://www.php-fig.org/psr/psr-7/)

Reference [phly/http](https://github.com/phly/http).

---

## Basic Usage

```php
use Wandu\Http\Cookie\CookieJarFactory;
use Wandu\Http\Psr\Factory\ServerRequestFactory;
use Wandu\Http\Psr\Factory\UploadedFileFactory;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\Sender\ResponseSender;
use Wandu\Http\Session\SessionFactory;
use Wandu\Http\Session\Adapter\FileAdapter;

$requestFactory = new ServerRequestFactory(new UploadedFileFactory());
$request = $requestFactory->fromGlobals();

$cookieFactory = new CookieJarFactory();
$cookie = $cookieFactory->fromServerRequest($request);

$sessionFactory = new SessionFactory(new FileAdapter(__DIR__ . '/_sess'));
$session = $sessionFactory->fromCookieJar($cookie);

// -- start controller --
//$cookie->set('cookie3', 'new33!!');
//$cookie->set('cookie4', 'new444');

//$session->set('sess3', '!!');
//$session->set('sess4', '??');

echo "<h3>Cookie!!</h3>";
echo "<pre>";
print_r($cookie->toArray());
echo "</pre>";
echo "<h3>Session!!</h3>";
echo "<pre>";
print_r($session->toArray());
echo "</pre>";

$response = new Response();

// -- end controller --
$sessionFactory->toCookieJar($session, $cookie);

$response = $cookieFactory->toResponse($cookie, $response);

(new ResponseSender)->send($response);

```

It's so simple! :D

## Api

### Stream

> `new Stream($stream = 'php://memory', $mode = 'r')`

#### Example.

Read / Write Stream.

```php
$stream = new Stream('php://memory', 'w+');
```

From HTTP Request body.

```php
$stream = new Stream('php://input');
```

### Message

> `new Message($protocolVersion = '1.1', array $headers = [], StreamInterface $body = null)`

### Response

> `new Response($statusCode = 200, $reasonPhrase = '', $protocolVersion = '1.1', array $headers = [], StreamInterface $body = null)`

### Request

> `new Request($method = null, UriInterface $uri = null, $protocolVersion = '1.1', array $headers = [], StreamInterface $body = null)`

### ServerReqeust

> __construct

```
@param array $serverParams
@param array $cookieParams
@param array $queryParams
@param array $uploadedFiles
@param array $parsedBody
@param array $attributes
@param string $method
@param \Psr\Http\Message\UriInterface|null $uri
@param string $protocolVersion
@param array $headers
@param \Psr\Http\Message\StreamInterface|null $body
```

You think it's too complicated. If you want more simple source, use factory.

```php
$requestFactory = new ServerRequestFactory(new UploadedFileFactory());
$serverRequest = $requestFactory->fromGlobals();

$serverRequest; // instanceof ServerRequestInterface
```


### UploadedFile

> `new UploadedFile($file = null, $size = null, $error = null, $clientFileName = null, $clientMediaType = null)`

### Uri

> `new Uri($uri)`

#### Uri::join

It executes like urljoin function in python.

```php
$uri = new Uri('http://wani.kr/hello/world');
$uriToJoin = new Uri('../other-link');

$uri->join($uriToJoin); // http://wani.kr/other-link
```

If you want to see more detail test cases, see
[this page](https://github.com/Wandu/Http/blob/master/tests/UriTest.php#L430).

#### Example.

```php
// case in-sensitive
new Uri('http://blog.wani.kr');
new Uri('http://BLOG.WANI.KR');
new Uri('https://blog.wani.kr');

// no scheme
new Uri('//blog.wani.kr');
new Uri('://blog.wani.kr');
new Uri('blog.wani.kr');

// real path
new Uri('/abc/def');

// relative path
new Uri('hello/world');

// user info
new Uri('http://wan2land@blog.wani.kr');
new Uri('http://wan2land:hello@blog.wani.kr');

// utf-8
new Uri('/hello/enwl dfk/-_-/한글'); // getPath -> '/hello/enwl%20dfk/-_-/%ED__%EA%B8_'

// query and fragment
new Uri('http://blog.wani.kr?hello=world&abc=def');
new Uri('http://blog.wani.kr/path/name?hello=world#fragment');
```
