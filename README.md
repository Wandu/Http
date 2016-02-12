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
use Wandu\Http\Psr\Factory\ResponseFactory;
use Wandu\Http\Psr\Factory\ServerRequestFactory;
use Wandu\Http\Psr\Factory\UploadedFileFactory;
use Wandu\Http\Psr\Sender\ResponseSender;
use Wandu\Http\Session\Adapter\FileAdapter;
use Wandu\Http\Session\SessionFactory;

$requestFactory = new ServerRequestFactory(new UploadedFileFactory());
$request = $requestFactory->fromGlobals();

$cookieManager = new CookieJarFactory();
$cookie = $cookieManager->fromServerRequest($request);

$sessionManager = new SessionFactory(new FileAdapter(__DIR__ . '/_sess'));
$session = $sessionManager->fromCookieJar($cookie);

$responseFactory = new ResponseFactory();
$response = $responseFactory->capture(function () use ($cookie, $session) {
    //$cookie->set('cookie3', 'new33!!');
    //$cookie->set('cookie4', 'new444');
    //
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
});

$sessionManager->toCookieJar($session, $cookie);
$response = $cookieManager->toResponse($cookie, $response);

$responseSender = new ResponseSender;
$responseSender->send($response);

```

It's so simple! :D

## Documents

### File Uploader

Wandu Http는 PSR7에서 사용하기 쉽게 몇가지 기능들을 제공하고 있습니다. 그 중 하나가 바로 File Uploader입니다.

**Example.**

```php
$request; // ServerRequestInterface.

$uploader = new \Wandu\Http\Psr\File\Uploader(__DIR__ . '/files');

$uploader->uploadFiles($request->getUploadedFiles()); // uploaded files' name return.
```

### Cookie

#### CookieJar

`Wandu\Http\Cookie\CookieJar` is implementation of `Wandu\Http\Contracts\CookieJarInterface`

```php
Wandu\Http\Cookie\CookieJar::get(string $name) :string

Wandu\Http\Cookie\CookieJar::set(string $name, string $value, DateTime $expire = null) :self

Wandu\Http\Cookie\CookieJar::has(string $name) :bool

Wandu\Http\Cookie\CookieJar::remove(string $name) :self
```

#### CookieJarFactory

This is useful for bringing a cookie jar object(`CookieJarInterface`) from the server request object(`ServerRequestInterface`). And a cookie jar object brought from the server request object must apply to the response object(`ResponseInterface`).

```php
// from ServerRequestInterface
Wandu\Http\Cookie\CookieJarFactory::fromServerRequest(
    Psr\Http\Message\ServerRequestInterface $request
) :Wandu\Http\Contracts\CookieJarInterface

// to ResponseInterface
Wandu\Http\Cookie\CookieJarFactory::toResponse(
    Wandu\Http\Contracts\CookieJarInterface $cookieJar,
    Psr\Http\Message\ResponseInterface $response
) :Psr\Http\Message\ResponseInterface
```

**Example.**

```php
use Wandu\Http\Cookie\CookieJarFactory;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\ServerRequest;

$cookieFactory = new CookieJarFactory();
$cookie = $cookieFactory->fromServerRequest($request); // request is a ServerRequest object.

// -- start controller --
//$cookie->set('cookie3', 'new33!!');
//$cookie->set('cookie4', 'new444');

$response = new Response();
$response = $cookieFactory->toResponse($cookie, $response);
```

### Session

#### Session

`Wandu\Http\Session\Session` is implementation of `Wandu\Http\Contracts\SessionInterface`

```php
Wandu\Http\Session\Session::getId() :string

Wandu\Http\Session\Session::get(string $name, mixed $default = null) :mixed

Wandu\Http\Session\Session::set(string $name, mixed $value) :self

Wandu\Http\Session\Session::flash(string $name, mixed $value) :self

Wandu\Http\Session\Session::has(string $name) :bool

Wandu\Http\Session\Session::remove(string $name) :self
```

#### SessionFactory

This is useful for bringing a session object(`SessionInterface`) from the cookie jar  object(`CookieJarInterface`). And a session object brought from the cookie jar object must apply to the same cookie jar object(`CookieJarInterface`).

```php
// from CookieJarInterface
Wandu\Http\Session\SessionFactory::fromCookieJar(
    Wandu\Http\Contracts\CookieJarInterface $cookieJar
) :Wandu\Http\Contracts\SessionInterface

// to CookieJarInterface
Wandu\Http\Session\SessionFactory::toCookieJar(
    Wandu\Http\Contracts\SessionInterface $session,
    Wandu\Http\Contracts\CookieJarInterface $cookieJar
) :void
```

**Example.**

```php
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Session\SessionFactory;
use Wandu\Http\Session\Adapter\FileAdapter;

$sessionManager = new SessionFactory(new FileAdapter(__DIR__ . '/_sess')); // default Adapter
$session = $sessionManager->fromCookieJar($cookie); // $cookie is the instance of CookieJarInterface

//$session->set('sess3', '!!');
//$session->set('sess4', '??');

$sessionManager->toCookieJar($session, $cookie);
```

### Session Adapter

There are two adapters.

 - `FileAdapter`
 - `RedisAdapter`
 
#### File Adapter

**Example.**

```php
$redisClient = new \Predis\Client();
$sessionManager = new SessionFactory(new RedisAdapter($redisClient));
```

#### Redis Adapter

**Example.**

```php
$sessionManager = new SessionFactory(new FileAdapter(__DIR__ . '/_sess'));
```

#### Global Adapter (for, refactoring only)

리팩토링 할 때만 사용하여 주십시오. 그 외에는 권장하지 않습니다.

**Example.**

```php
$sessionManager = new SessionFactory(new GlobalAdapter());
```

### Parameter

#### Parameter


### Psr7 Implementations

#### Stream

```php
Wandu\Http\Psr\Stream::__construct(
    string $stream = 'php://memory',
    string $mode = 'r'
)
```

**Example.**

Read / Write Stream.

```php
$stream = new Stream('php://memory', 'w+');
```

From HTTP Request body.

```php
$stream = new Stream('php://input');
```

#### Message

```php
Wandu\Http\Psr\Message::__construct(
    string $protocolVersion = '1.1',
    array $headers = [],
    Psr\Http\Message\StreamInterface $body = null
)
```

#### Response

```php
Wandu\Http\Psr\Response::__construct(
    int $statusCode = 200,
    string $reasonPhrase = '',
    string $protocolVersion = '1.1',
    array $headers = [],
    Psr\Http\Message\StreamInterface $body = null
)
```

#### Request

```php
Wandu\Http\Psr\Request::__construct(
    string $method = null,
    Psr\Http\Message\UriInterface $uri = null,
    string $protocolVersion = '1.1',
    array $headers = [],
    Psr\Http\Message\StreamInterface $body = null
)
```

#### ServerRequest

```php
Wandu\Http\Psr\ServerRequest::__construct(
    array $serverParams = [],
    array $cookieParams = [],
    array $queryParams = [],
    array $uploadedFiles = [],
    array $parsedBody = [],
    array $attributes = [],
    string $method = null,
    Psr\Http\Message\UriInterface $uri = null,
    string $protocolVersion = '1.1',
    array $headers = [],
    Psr\Http\Message\StreamInterface $body = null
)
```

You think it's too complicated. If you want more simple source, use [ServerRequestFactory](#serverrequestfactory).

#### UploadedFile

```php
Wandu\Http\Psr\UploadedFile::__construct(
    string $file = null,
    int $size = null,
    int $error = null,
    string $clientFileName = null,
    string $clientMediaType = null
)
```

You think it's too complicated also. If you want more simple source, use [UploadedFileFactory](#uploadedfilefactory).

#### Uri

```php
Wandu\Http\Psr\Uri::__construct(
    string $uri
)

Wandu\Http\Psr\Uri::join(
    Wandu\Http\Psr\Uri $uri
)
```

**Wandu\Http\Psr\Uri::join**

It executes like urljoin function in python.

```php
$uri = new Uri('http://wani.kr/hello/world');
$uriToJoin = new Uri('../other-link');

$uri->join($uriToJoin); // http://wani.kr/other-link
```

If you want to see more detail test cases, see
[this page](https://github.com/Wandu/Http/blob/master/tests/UriTest.php#L430).

**Example.**

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

### Factory Classes

#### UploadedFileFactory

```php
Wandu\Http\Psr\Factory\UploadedFileFactory::fromFiles(
    array $files
)
```

**Example.**

```php
use Wandu\Http\Psr\Factory\UploadedFileFactory;

$factory = new UploadedFileFactory();
$treeOfFiles = $factory->fromFiles($_FILES);
$treeOfFiles; // array of UploadedFile object.
```

#### ServerRequestFactory

```php
Wandu\Http\Psr\Factory\ServerRequestFactory::__construct(
    Wandu\Http\Psr\Factory\UploadedFileFactory $fileFactory
)

Wandu\Http\Psr\Factory\ServerRequestFactory::fromGlobals()
```

**Example.**

```php
use Wandu\Http\Psr\Factory\ServerRequestFactory;
use Wandu\Http\Psr\Factory\UploadedFileFactory;

$requestFactory = new ServerRequestFactory(new UploadedFileFactory());
$serverRequest = $requestFactory->fromGlobals();

$serverRequest; // instanceof ServerRequestInterface
```

#### ResponseSender

```php
Wandu\Http\Psr\Sender\ResponseSender::sendToGlobal(
    Psr\Http\Message\ResponseInterface $response
) :void
```
