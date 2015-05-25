Wandu Session
===

Session Based on PSR-7(exactly based on PSR-7 ServerRequest's cookie).

## Basic Usage

```php
<?php
namespace Your\OwnNamespace;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Session\Provider\FileProvider;
use Wandu\Session\Session;

$request = ''; // PSR7 ServerReqeustInterface
$response = ''; // PSR7 ResponseInterface

$session = new Session('YourOwnSessName', $request, new FileProvider(__DIR__ . '/sessions'));
$response = $session->applyResponse($response);

$session->get('hello');
$session->set('hello', 'what?');
```

That's too simple. :D