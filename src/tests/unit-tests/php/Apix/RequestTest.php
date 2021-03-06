<?php

/**
 *
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license     http://opensource.org/licenses/BSD-3-Clause  New BSD License
 *
 */

namespace Apix;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zenya_Api_Request
     */
    protected $request = null;

    protected function setUp()
    {
        $this->request = new Request();
    }

    protected function tearDown()
    {
        unset($this->request);
    }

    public function testGetSetUri()
    {
        $this->assertSame('/', $this->request->getUri() );
        $this->request->setUri('/qwerty/');
        $this->assertSame('/qwerty', $this->request->getUri() );
    }

    /**
     * @dataProvider headersProvider
    */
    public function testGetUriWithHttpHeaders($str)
    {
        $_SERVER[$str] = "/$str";
        $this->request->setUri();
        $this->assertSame($_SERVER[$str], $this->request->getUri());
        $_SERVER[$str] = null;
    }

    public function headersProvider()
    {
        return array(
            array('HTTP_X_REWRITE_URL'),
            array('REQUEST_URI'),
            array('PATH_INFO'),
            array('ORIG_PATH_INFO')
        );
    }

    public function testGetUriWithIIS_WasUrlRewritten()
    {
        $_SERVER['IIS_WasUrlRewritten'] = '1';
        $_SERVER['UNENCODED_URL'] = '/IIS_WasUrlRewritten';
        $this->request->setUri();
        $this->assertSame('/IIS_WasUrlRewritten', $this->request->getUri());
        $_SERVER['UNENCODED_URL'] = null;
    }

    public function testGetSetParam()
    {
        $this->request->setParam('hello', 'world');
        $this->assertEquals('world', $this->request->getParam('hello') );
    }

    public function testGetParamWithPosixFilters()
    {
        $this->request->setParam('arg', '#a%20zerty+&%$1-23.4567');

        // alnum, alpha, digit
        $this->assertSame('azerty1234567', $this->request->getParam('arg', false, 'alnum') );
        $this->assertSame('azerty', $this->request->getParam('arg', false, 'alpha') );
        $this->assertSame('1234567', $this->request->getParam('arg', false, 'digit') );
    }

    public function testGetRawParamWithPosixFilters()
    {
        $this->request->setParam('arg', '#a%20zerty+&%$1-23.4567');

        $this->assertSame('a20zerty1234567', $this->request->getParam('arg', true, 'alnum') );
        $this->assertSame('azerty', $this->request->getParam('arg', true, 'alpha') );
        $this->assertSame('201234567', $this->request->getParam('arg', true, 'digit') );
    }

    public function testGetSetParams()
    {
        $this->request->setParams(array('a', 'b', 'c'));
        $this->assertSame(array('a', 'b', 'c'), $this->request->getParams() );

        $this->request->setParams();
        $this->assertSame($_REQUEST, $this->request->getParams() );
    }

    /*
    *  I'm running into a problem.  Filters to searches are typically passed in as an array.  APIx appears unable to handle these scenarios in GET requests, as it assumes a key=>value where the value is a string, and runs rawurldecode() on it.  So, when a query like this comes in:
    *   GET v1/categories/search/chicago+hotels?filters[0][0]=0&filters[0][1][0]=1
    *   It fails:
    *   Warning: rawurldecode() expects parameter 1 to be string, array given in phar:///home/jspalink/dev/zenya/www/zenya-stack-api/lib/apix.phar/src/php/Apix/Request.php on line 135
    *  This error occurs only for GET requests.  POST works as expected.
    */
    public function testGetParamWithArray()
    {
        $r = array('filters' => array(0=>0, 1=>1));
        $this->request->setParams($r);
        $this->assertSame($r['filters'], $this->request->getParam('filters', true) );
    }

    public function testGetSetMethod()
    {
        $this->assertSame('GET', $this->request->getMethod() );

        $_SERVER['REQUEST_METHOD'] = 'REQUEST_METHOD';
        $this->request->setMethod();
        $this->assertSame('REQUEST_METHOD', $this->request->getMethod() );

        $this->request->setParam('_method', 'qs');
        $this->request->setMethod();
        $this->assertSame('QS', $this->request->getMethod() );

        $this->request->setHeader('X-HTTP-Method-Override', 'head');
        $this->request->setMethod();
        $this->assertSame('HEAD', $this->request->getMethod() );

        $this->request->setMethod('methd');
        $this->assertSame('METHD', $this->request->getMethod(), 'Should go all uppercase');
    }

    public function testGetSetHeaderIsCaseInsensitive()
    {
        $this->request->setHeader('fOo', 'bar');
        $this->assertSame('bar', $this->request->getHeader('FoO') );
    }

    public function testGetSetHeaders()
    {
        $this->request->setHeaders(array('a', 'b', 'c'));
        $this->assertSame(array('a', 'b', 'c'), $this->request->getHeaders() );

        $this->request->setHeaders();
        $this->assertSame($_SERVER, $this->request->getHeaders() );
    }

    public function testGetIp()
    {
        $this->request->setHeader('REMOTE_ADDR', '1.');
        $this->assertSame('1.', $this->request->getIp() );

        $this->request->setHeader('HTTP_X_FORWARDED_FOR', '2.');
        $this->assertSame('2.', $this->request->getIp() );

        $this->request->setHeader('HTTP_CLIENT_IP', '3.');
        $this->assertSame('3.', $this->request->getIp() );
    }

    public function testSetBodyFromStream()
    {
        $this->request->setBody();
        $this->assertSame('', $this->request->getBody());

        $this->request->setBodyStream(APP_TESTDIR . '/Apix/Fixtures/body.txt');

        $this->request->setBody();

        $this->assertSame('body1=value1&body2=value2', $this->request->getBody());
    }

    public function testHasBody()
    {
        $this->request->setBody('');
        $this->assertSame('', $this->request->getBody());

        $this->assertFalse($this->request->hasBody());

        $this->request->setBody('body-data');

        $this->assertTrue($this->request->hasBody());

    }

    protected $data = <<<DATA
// RFC 2616 defines 'deflate' encoding as zlib format from RFC 1950,
// while many applications send raw deflate stream from RFC 1951.
// We should check for presence of zlib header and use gzuncompress() or
// gzinflate() as needed. See bug #15305
DATA;

    public function testGetSetBodyDeflate()
    {
        $raw = gzdeflate($this->data);
        $this->request->setHeader('content-encoding', 'deflate');
        $this->request->setBody($raw);
        $this->assertSame($this->data, $this->request->getBody(false));
        $this->assertSame($raw, $this->request->getRawBody());
    }

    public function testGetSetBodyGzip()
    {
        $raw = gzencode($this->data);
        $this->request->setHeader('content-encoding', 'gzip');
        $this->request->setBody($raw);
        $this->assertSame($this->data, $this->request->getBody(false));
        $this->assertSame($raw, $this->request->getRawBody());
    }

    public function testGetSetBody()
    {
        $raw = $this->data;
        $this->request->setHeader('content-encoding', 'hashed');
        $this->request->setBody($raw);
        $this->assertSame($this->data, $this->request->getBody(false));
        $this->assertSame($raw, $this->request->getRawBody());
    }

    public function testGetSetBodyCache()
    {
        $raw = gzencode($this->data);
        $this->request->setBody($raw);
        $this->assertSame($this->data, $this->request->getBody());
        $this->assertSame($raw, $this->request->getRawBody());
    }

    /*
        There is no parameter cleaning, which means that what actually gets
        passed in in the "keyword" parameter is "red%20racing%20cars". Would
        it make sense to always use rawurldecode on parameters in Apix itself,
        rather than having to do that in the route definitions?
        - Jonathan
     */
    public function testGetParamFiltered()
    {
        $this->request->setParam('hello', 'plain%20world');
        $this->assertEquals('plain world', $this->request->getParam('hello') );
    }

    public function testGetRawParamFiltered()
    {
        $this->request->setParam('hello', 'plain%20world');
        $this->assertEquals('plain%20world', $this->request->getParam('hello', true) );
    }

}
