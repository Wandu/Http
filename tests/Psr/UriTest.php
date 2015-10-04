<?php
namespace Wandu\Http\Psr;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Mockery;

class UriTest extends PHPUnit_Framework_TestCase
{
    public function testGetScheme()
    {
        $uri = new Uri('http://blog.wani.kr');
        $this->assertSame('http', $uri->getScheme());

        $uri = new Uri('https://blog.wani.kr');
        $this->assertSame('https', $uri->getScheme());

        // If no scheme is present, this method MUST return an empty string.
        $uri = new Uri('//blog.wani.kr');
        $this->assertSame('', $uri->getScheme());

        $uri = new Uri('blog.wani.kr');
        $this->assertSame('', $uri->getScheme());

        // The value returned MUST be normalized to lowercase, per RFC 3986
        // Section 3.1.
        $uri = new Uri('HTTP://blog.wani.kr');
        $this->assertSame('http', $uri->getScheme());

        // The trailing ":" character is not part of the scheme and MUST NOT be
        // added.
        $uri = new Uri('://blog.wani.kr');
        $this->assertSame('', $uri->getScheme());

        // Implementations MUST support the schemes "http" and "https" case
        // insensitively, and MAY accommodate other schemes if required. (@withScheme)
        try {
            new Uri('ftp://blog.wani.kr');
            $this->fail();
        } catch (InvalidArgumentexception $e) {
            $this->assertEquals('Unsupported scheme "ftp".', $e->getMessage());
        }
    }

    public function testGetAuthority()
    {
        //If no authority information is present, this method MUST return an empty
        //string.
        $uri = new Uri('/abc/def');
        $this->assertSame('', $uri->getAuthority());

        // The authority syntax of the URI is:

        //<pre>
        //[user-info@]host[:port]
        //</pre>

        $uri = new Uri('http://blog.wani.kr');
        $this->assertSame('blog.wani.kr', $uri->getAuthority());

        $uri = new Uri('http://wan2land@blog.wani.kr');
        $this->assertSame('wan2land@blog.wani.kr', $uri->getAuthority());

        $uri = new Uri('http://wan2land@blog.wani.kr:8080');
        $this->assertSame('wan2land@blog.wani.kr:8080', $uri->getAuthority());

        // If the port component is not set or is the standard port for the current
        // scheme, it SHOULD NOT be included.
        $uri = new Uri('http://wan2land@blog.wani.kr:80');
        $this->assertSame('wan2land@blog.wani.kr', $uri->getAuthority());
    }

    public function testGetUserInfo()
    {
        // If no user information is present, this method MUST return an empty
        // string.
        $uri = new Uri('/abc/def');
        $this->assertSame('', $uri->getUserInfo());

        // If a user is present in the URI, this will return that value;
        // additionally, if the password is also present, it will be appended to the
        // user value, with a colon (":") separating the values.

        // The trailing "@" character is not part of the user information and MUST
        // NOT be added.
        $uri = new Uri('http://wan2land@blog.wani.kr');
        $this->assertSame('wan2land', $uri->getUserInfo());

        $uri = new Uri('http://wan2land:hello@blog.wani.kr');
        $this->assertSame('wan2land:hello', $uri->getUserInfo());
    }

    public function testGetHost()
    {
        $uri = new Uri('http://blog.wani.kr');
        $this->assertEquals('blog.wani.kr', $uri->getHost());

        // If no host is present, this method MUST return an empty string.
        $uri = new Uri('/hello/world');
        $this->assertEquals('', $uri->getHost());

        // The value returned MUST be normalized to lowercase, per RFC 3986
        // Section 3.2.2.
        $uri = new Uri('http://BLOG.WANI.KR');
        $this->assertEquals('blog.wani.kr', $uri->getHost());
    }

    public function testGetPort()
    {
        // If a port is present, and it is non-standard for the current scheme,
        // this method MUST return it as an integer.
        $uri = new Uri('http://blog.wani.kr:8080');
        $this->assertSame(8080, $uri->getPort());

        // If the port is the standard port used with the current scheme,
        // this method SHOULD return null.
        $uri = new Uri('http://blog.wani.kr:80');
        $this->assertnull($uri->getPort());

        // If no port is present, and no scheme is present, this method MUST return
        // a null value.
        $uri = new Uri('/hello/world');
        $this->assertNull($uri->getPort());

        // If no port is present, but a scheme is present,
        // this method MAY return the standard port for that scheme, but SHOULD return null.
        $uri = new Uri('http://blog.wani.kr');
        $this->assertNull($uri->getPort());
    }

    public function testGetPath()
    {
        $uri = new Uri('http://blog.wani.kr');
        $this->assertSame('', $uri->getPath());

        // The path can either be empty or absolute (starting with a slash) or
        // rootless (not starting with a slash). Implementations MUST support all
        // three syntaxes.
        $uri = new Uri('http://blog.wani.kr/');
        $this->assertSame('/', $uri->getPath());

        $uri = new Uri('http://blog.wani.kr/abc/def');
        $this->assertSame('/abc/def', $uri->getPath());

        $uri = new Uri('hello/world');
        $this->assertSame('hello/world', $uri->getPath());

        // Normally, the empty path "" and absolute path "/" are considered equal as
        // defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
        // do this normalization because in contexts with a trimmed base path, e.g.
        // the front controller, this difference becomes significant. It's the task
        // of the user to handle both "" and "/".
        $uri = new Uri('');
        $this->assertSame('', $uri->getPath());

        $uri = new Uri('/');
        $this->assertSame('/', $uri->getPath());

        // The value returned MUST be percent-encoded, but MUST NOT double-encode
        // any characters. To determine what characters to encode, please refer to
        // RFC 3986, Sections 2 and 3.3.

        // As an example, if the value should include a slash ("/") not intended as
        // delimiter between path segments, that value MUST be passed in encoded
        // form (e.g., "%2F") to the instance.
        $uri = new Uri('/hello/enwl dfk/-_-/한글');
        $this->assertSame('/hello/enwl%20dfk/-_-/%ED%95%9C%EA%B8%80', $uri->getPath());
    }

    public function testGetQuery()
    {
        // If no query string is present, this method MUST return an empty string.
        $uri = new Uri('http://blog.wani.kr');
        $this->assertSame('', $uri->getQuery());

        // The leading "?" character is not part of the query and MUST NOT be
        // added.
        $uri = new Uri('http://blog.wani.kr?hello=world&abc=def');
        $this->assertSame('hello=world&abc=def', $uri->getQuery());

        // The value returned MUST be percent-encoded, but MUST NOT double-encode
        // any characters. To determine what characters to encode, please refer to
        // RFC 3986, Sections 2 and 3.4.

        // As an example, if a value in a key/value pair of the query string should
        // include an ampersand ("&") not intended as a delimiter between values,
        // that value MUST be passed in encoded form (e.g., "%26") to the instance.
        $uri = new Uri('http://blog.wani.kr?hello=world&한글=def');
        $this->assertSame('hello=world&%ED%95%9C%EA%B8%80=def', $uri->getQuery());
    }

    public function testGetFragment()
    {
        // If no fragment is present, this method MUST return an empty string.
        $uri = new Uri('http://blog.wani.kr');
        $this->assertSame('', $uri->getFragment());

        // The leading "#" character is not part of the fragment and MUST NOT be
        // added.
        $uri = new Uri('http://blog.wani.kr#helloworld');
        $this->assertSame('helloworld', $uri->getFragment());

        // The value returned MUST be percent-encoded, but MUST NOT double-encode
        // any characters. To determine what characters to encode, please refer to
        // RFC 3986, Sections 2 and 3.5.
        $uri = new Uri('http://blog.wani.kr#한글은한글은');
        $this->assertSame('%ED%95%9C%EA%B8%80%EC%9D%80%ED%95%9C%EA%B8%80%EC%9D%80', $uri->getFragment());
    }


    public function testWithScheme()
    {
        $uri = new Uri('http://blog.wani.kr');

        $this->assertNotSame($uri, $uri->withScheme('http'));

        // This method MUST retain the state of the current instance, and return
        // an instance that contains the specified scheme.
        $this->assertSame('https', $uri->withScheme('https')->getScheme());
        $this->assertSame('http', $uri->withScheme('http')->getScheme());

        // Implementations MUST support the schemes "http" and "https" case
        // insensitively, and MAY accommodate other schemes if required.
        try {
            $uri->withScheme('sftp');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Unsupported scheme "sftp".', $e->getMessage());
        }

        // An empty scheme is equivalent to removing the scheme.
        $this->assertSame('', $uri->withScheme('')->getScheme());

        // The trailing ":" character is not part of the scheme and MUST NOT be
        // added. (@getScheme)
        $this->assertSame('https', $uri->withScheme('https:')->getScheme());
        $this->assertSame('https', $uri->withScheme('https://')->getScheme());
    }

    public function testWithUserInfo()
    {
        $uri = new Uri('http://blog.wani.kr');

        $this->assertNotSame($uri, $uri->withUserInfo('blog.wani.kr'));

        // This method MUST retain the state of the current instance, and return
        // an instance that contains the specified user information.
        $this->assertSame('blabla', $uri->withUserInfo('blabla')->getUserInfo());

        // Password is optional, but the user information MUST include the
        // user; an empty string for the user is equivalent to removing user
        // information.
        $this->assertSame('blabla:password', $uri->withUserInfo('blabla', 'password')->getUserInfo());
        $this->assertSame('', $uri->withUserInfo('')->getUserInfo());
    }

    public function testWithHost()
    {
        $uri = new Uri('http://blog.wani.kr');

        $this->assertNotSame($uri, $uri->withHost('blog.wani.kr'));

        // This method MUST retain the state of the current instance, and return
        // an instance that contains the specified host.
        $this->assertSame('blabla', $uri->withHost('blabla')->getHost());

        // An empty host value is equivalent to removing the host.
        $this->assertSame('', $uri->withHost('')->getHost());

        // The value returned MUST be normalized to lowercase, per RFC 3986
        // Section 3.2.2. (@getHost)
        $this->assertSame('helloworld.com', $uri->withHost('HelloWORLD.COM')->getHost());
    }


    public function testWithPort()
    {
        $uri = new Uri('http://blog.wani.kr:8001');

        $this->assertNotSame($uri, $uri->withPort(8080));

        // This method MUST retain the state of the current instance, and return
        // an instance that contains the specified port.
        $this->assertSame(8888, $uri->withPort(8888)->getPort());

        // Implementations MUST raise an exception for ports outside the
        // established TCP and UDP port ranges.
        try {
            $uri->withPort(0);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid port "0". It must be a valid TCP/UDP port.', $e->getMessage());
        }
        try {
            $uri->withPort(65536);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid port "65536". It must be a valid TCP/UDP port.', $e->getMessage());
        }

        // A null value provided for the port is equivalent to removing the port
        // information.
        $this->assertNull($uri->withPort(null)->getPort());

        // If a port is present, and it is non-standard for the current scheme,
        // this method MUST return it as an integer. (@getPort)
        $this->assertSame(8080, $uri->withPort("8080")->getPort());
    }

    public function testWithPath()
    {
        $uri = new Uri('http://blog.wani.kr/hello/world');

        $this->assertNotSame($uri, $uri->withPath('/abc/def'));

        // This method MUST retain the state of the current instance, and return
        // an instance that contains the specified path.
        $this->assertSame('/abc/def', $uri->withPath('/abc/def')->getPath());

        // The path can either be empty or absolute (starting with a slash) or
        // rootless (not starting with a slash). Implementations MUST support all
        // three syntaxes.
        $this->assertSame('/', $uri->withPath('/')->getPath());
        $this->assertSame('', $uri->withPath('')->getPath());
        $this->assertSame('abc/def', $uri->withPath('abc/def')->getPath());

        // If the path is intended to be domain-relative rather than path relative then
        // it must begin with a slash ("/"). Paths not starting with a slash ("/")
        // are assumed to be relative to some base path known to the application or
        // consumer.

        // Users can provide both encoded and decoded path characters.
        // Implementations ensure the correct encoding as outlined in getPath().


        try {
            $uri->withPath([]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid path "array". It must be a string.', $e->getMessage());
        }
        try {
            $uri->withPath('?hello');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid path "?hello". It must not contain a query string.', $e->getMessage());
        }
        try {
            $uri->withPath('#blabla');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid path "#blabla". It must not contain a URI fragment.', $e->getMessage());
        }

    }

    public function testWithQuery()
    {
        $uri = new Uri('http://blog.wani.kr/?foo=bar');

        $this->assertNotSame($uri, $uri->withQuery('hello=world'));

        // This method MUST retain the state of the current instance, and return
        // an instance that contains the specified query string.
        $this->assertSame('hello=world', $uri->withQuery('hello=world')->getQuery());

        // Users can provide both encoded and decoded query characters.
        // Implementations ensure the correct encoding as outlined in getQuery().

        // An empty query string value is equivalent to removing the query string.
        $this->assertSame('', $uri->withQuery('')->getQuery());


        try {
            $uri->withQuery([]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid query "array". It must be a string.', $e->getMessage());
        }
        try {
            $uri->withQuery('#hello');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid query "#hello". It must not contain a URI fragment.', $e->getMessage());
        }
    }


    public function testWithFragment()
    {
        $uri = new Uri('http://blog.wani.kr/#foo');

        $this->assertNotSame($uri, $uri->withfragment('whatthe'));

        // This method MUST retain the state of the current instance, and return
        // an instance that contains the specified URI fragment.
        $this->assertSame('whatthe', $uri->withfragment('whatthe')->getFragment());

        // Users can provide both encoded and decoded fragment characters.
        // Implementations ensure the correct encoding as outlined in getFragment().

        // An empty fragment value is equivalent to removing the fragment.
        $this->assertSame('', $uri->withfragment('')->getFragment());
    }

    public function testToString()
    {
        $uri = new Uri('http://blog.wani.kr#fragment');
        $this->assertEquals('http://blog.wani.kr#fragment', $uri->__toString());

        $uri = new Uri('http://blog.wani.kr/path/name?hello=world#fragment');
        $this->assertEquals('http://blog.wani.kr/path/name?hello=world#fragment', $uri->__toString());

        $uri = (new Uri('http://blog.wani.kr/path/name?hello=world#fragment'))->withPath('hello/world');
        $this->assertEquals('http://blog.wani.kr/hello/world?hello=world#fragment', $uri->__toString());

        $uri = new Uri('nothing#frag');
        $this->assertEquals('nothing#frag', $uri->__toString());
    }

    public function testBugFix()
    {
        $uri = new Uri('abcd.com');
        $this->assertEquals('http://abcd.com', $uri->withScheme('http')->__toString());

        $uri = new Uri('abcd.com/abc/def');
        $this->assertEquals('http://abcd.com/abc/def', $uri->withScheme('http')->__toString());
    }

    public function urlProvider()
    {
        return [
            // default
            [
                'http://wani.kr',
                '/move-to-page',
                'http://wani.kr/move-to-page'
            ],

            // fragment set
            [
                'http://wani.kr?query=query#fragment',
                '/move-to-page',
                'http://wani.kr/move-to-page'
            ],
            [
                'http://wani.kr',
                '/move-to-page?query=query#fragment',
                'http://wani.kr/move-to-page?query=query#fragment'
            ],
            [
                'http://wani.kr?query=other#theotherfragment',
                '/move-to-page?query=query#fragment',
                'http://wani.kr/move-to-page?query=query#fragment'
            ],

            // other target
            [
                'http://wani.kr',
                '../../../move-to-page',
                'http://wani.kr/move-to-page'
            ],
            [
                'http://wani.kr',
                './move-to-page',
                'http://wani.kr/move-to-page'
            ],

            // file + target
            [
                'http://wani.kr/current/next/and-next',
                '/front/login/page',
                'http://wani.kr/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next',
                'front/login/page',
                'http://wani.kr/current/next/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next',
                './front/login/page',
                'http://wani.kr/current/next/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next',
                '../front/login/page',
                'http://wani.kr/current/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next',
                '../front/../page',
                'http://wani.kr/current/page'
            ],
            [
                'http://wani.kr/current/next/and-next',
                '',
                'http://wani.kr/current/next/and-next'
            ],
            [
                'http://wani.kr/current/next/and-next',
                '.',
                'http://wani.kr/current/next/and-next'
            ],
            [
                'http://wani.kr/current/next/and-next',
                '/',
                'http://wani.kr'
            ],

            // directory + target
            [
                'http://wani.kr/current/next/and-next/',
                '/front/login/page',
                'http://wani.kr/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                'front/login/page',
                'http://wani.kr/current/next/and-next/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                './front/login/page',
                'http://wani.kr/current/next/and-next/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                '../front/login/page',
                'http://wani.kr/current/next/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                '',
                'http://wani.kr/current/next/and-next/'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                '.',
                'http://wani.kr/current/next/and-next/'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                '/',
                'http://wani.kr'
            ],
        ];
    }

    /**
     * @dataProvider urlProvider
     */
    public function testJoin($base, $target, $expected)
    {
        $this->assertSame($expected, (new Uri($base))->join(new Uri($target))->__toString());
    }

    public function testUnicodeUrl()
    {
        $uri1 = new Uri('http://my.test.com/%EC%BD%94%EB%93%9C%EC%9D%B4%EA%B7%B8%EB%82%98%EC%9D%B4%ED%84%B0');
        $uri2 = new Uri('http://my.test.com/코드이그나이터');

        $this->assertEquals($uri1->getPath(), $uri2->getPath());
    }
}
