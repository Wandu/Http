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

## Document

### UploadedFileFactory

> `UploadedFileFactory::fromFiles($_FILES)`

### ServerRequestFactory

> `ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES)`

### ResponseSender

> `ResponseSender::send(ResponseInterface $response)`

### Example

```php
$app = new Your\Own\Application();
$request = Wandu\Http\Factory\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$response = $app->execute($request);
Wandu\Http\Sender\ResponseSender::send($response);
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

> `new Message($protocolVersion, array $headers = [], StreamInterface $body = null)`

### Response

> `new Response($statusCode = 200, $reasonPhrase = '', array $headers = [], StreamInterface $body = null)`

### Request

> `new Request($httpVersion, $method = null, UriInterface $uri = null, array $headers = [], StreamInterface $body = null)`

### ServerReqeust

> `new ServerRequest(array $serverParams = [], array $cookieParams = [], array $queryParams = [], array $uploadedFiles = [], $parsedBody = [], array $attributes = [], $httpVersion = '1.1', UriInterface $uri = null)`

You think it's too complicated. But, Let's see the next example!

#### Example.

```php
$uri = new Uri();
$request = new ServerRequest($_SERVER, $_COOKIE, $_GET, UploadedFileFactory::fromFiles($_FILES), $_POST, [], '1.1', $uri);
```

If you want more simple source, use factory.

### UploadedFile

> `new UploadedFile($file = null, $size = null, $error = null, $clientFileName = null, $clientMediaType = null)`

### Uri

> `new Uri($uri)`

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
