<?php

use Mockery as m;
use Fideloper\ConditionalRequest\Request\Symfony;
use Symfony\Component\HttpFoundation\Request;

class SymfonyTest extends PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    public function testGetIfNoneMatch()
    {
        $request = $this->getRequest();
        $symfony = new Symfony( $request );

        $etags = $symfony->getIfNoneMatch();

        $this->assertTrue( is_array($etags), 'ETags returned from Symfony request is an array' );
        $this->assertEquals( $etags[0], '1234', 'Etag returned from "mock" equals 1234' );
    }

    public function testGetIfMatch()
    {
        $request = $this->getRequest();
        $symfony = new Symfony( $request );

        $etag = $symfony->getIfMatch();

        $this->assertTrue( is_string($etag), 'ETag returned from Symfony request is an string' );
        $this->assertEquals( $etag, 'abcd', 'Etag returned from "mock" equals abcd' );
    }

    public function testGetIfModifiedSince()
    {
        $request = $this->getRequest();
        $symfony = new Symfony( $request );

        $expected = DateTime::createFromFormat(DATE_RFC2822, 'Wed, 15 May 2013 20:12:27 GMT');

        $modifiedSince = $symfony->getIfModifiedSince();

        $this->assertInstanceOf( 'DateTime', $modifiedSince, 'Modified Since date is instance of DateTime' );
        $this->assertEquals( $expected->getTimestamp(), $modifiedSince->getTimestamp() );
    }

    public function testGetIfUnModifiedSince()
    {
        $request = $this->getRequest();
        $symfony = new Symfony( $request );

        $expected = DateTime::createFromFormat(DATE_RFC2822, 'Tue, 14 May 2013 20:12:27 GMT');

        $unmodifiedSince = $symfony->getIfUnmodifiedSince();

        $this->assertInstanceOf( 'DateTime', $unmodifiedSince, 'Modified Since date is instance of DateTime' );
        $this->assertEquals( $expected->getTimestamp(), $unmodifiedSince->getTimestamp() );
    }

    public function testHasAttemptedEtag()
    {
        $request = $this->getRequest();
        $symfony = new Symfony( $request );

        $this->assertTrue( $symfony->attemptedEtag('if-none-match') );
    }

    public function testHasNotAttemptedEtag()
    {
        $request = new Request(
            array(), // Query
            array(), // Request (POST)
            array(), // Attributes (PATH_INFO, etc)
            array(), // Cookies
            array(), // Files
            array(
                'HTTP_IF_MATCH' => '1111',
                'HTTP_IF_MODIFIED_SINCE' => 'Wed, 15 May 2013 20:12:27 GMT',
                'HTTP_IF_UNMODIFIED_SINCE' => 'Tue, 14 May 2013 20:12:27 GMT',
            ), // Server
            null     // Content
        );

        $symfony = new Symfony( $request );

        $this->assertFalse( $symfony->attemptedEtag('if-none-match') );
    }

    protected function getRequest()
    {
        return new Request(
            array(), // Query
            array(), // Request (POST)
            array(), // Attributes (PATH_INFO, etc)
            array(), // Cookies
            array(), // Files
            array(
                'HTTP_IF_NONE_MATCH' => '1234',
                'HTTP_IF_MATCH' => 'abcd',
                'HTTP_IF_MODIFIED_SINCE' => 'Wed, 15 May 2013 20:12:27 GMT',
                'HTTP_IF_UNMODIFIED_SINCE' => 'Tue, 14 May 2013 20:12:27 GMT',
            ), // Server
            null     // Content
        );
    }

}