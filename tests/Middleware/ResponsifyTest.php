<?php
namespace Wandu\Http\Middleware;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Wandu\Http\Psr\Factory\ResponseFactory;

class ResponsifyTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testReturnNull()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('', $response->getBody()->__toString());
    }

    public function testReturnString()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return "Hello World";
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('Hello World', $response->getBody()->__toString());
    }

    public function testReturnInteger()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return (int) 3182;
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('3182', $response->getBody()->__toString());
    }

    public function testReturnBooleanFalse()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return false;
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('false', $response->getBody()->__toString());
    }

    public function testReturnBooleanTrue()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return true;
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('true', $response->getBody()->__toString());
    }

    public function testReturnFloat()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return 1.001;
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('1.001', $response->getBody()->__toString());
    }

    public function testReturnArray()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return [
                'foo' => 'foo string',
                'bar' => 3030,
            ];
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame([
            'foo' => 'foo string',
            'bar' => 3030,
        ], json_decode($response->getBody()->__toString(), true));
    }

    public function testReturnObject()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return (object) [
                'foo' => 'foo string',
                'bar' => 3030,
            ];
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame([
            'foo' => 'foo string',
            'bar' => 3030,
        ], json_decode($response->getBody()->__toString(), true));
    }

    public function testReturnReadableResource()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return fopen(__DIR__ . '/stub-text.txt', 'r');
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame("stub-text.txt contents\n", $response->getBody()->__toString());
    }

    public function testReturnUnreadableResource()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        try {
            $responsify->handle($request, function () {
                return fopen(__DIR__ . '/stub-text.txt', 'a');
            });
            $this->fail();
        } catch (Runtimeexception $e) {
            $this->assertEquals('Unsupported Type of Response.', $e->getMessage());
        }
    }

    public function testReturnCallable()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return function () {
                return "Hello Word!";
            };
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame("Hello Word!", $response->getBody()->__toString());
    }

    public function testReturnMultipleCallable()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return function () {
                return function () {
                    return "Hello Word2!";
                };
            };
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame("Hello Word2!", $response->getBody()->__toString());
    }

    public function testReturnYield()
    {
        $responseFactory = new ResponseFactory();
        $responsify = new Responsify($responseFactory);

        $request = Mockery::mock(ServerRequestInterface::class);

        $response = $responsify->handle($request, function () {
            return function () {
                for ($i = 0; $i < 10; $i++) {
                    yield $i;
                }
            };
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame("0123456789", $response->getBody()->__toString());
    }
}
