<?php

use Mockery as m;
use Fideloper\ConditionalRequest\Conditional;

class ConcurrencyControlTest extends PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    public function testDoUpdateIfMatchReturnsFalse()
    {
        $request = m::mock('Fideloper\ConditionalRequest\Request\RequestInterface');
        $request->shouldReceive('getIfMatch')->once()->andReturn( '1234' );

        $conditional = new Conditional( $request );
        $conditional->setEtag('abcd'); // This will not match requests ETag

        // False - don't do update
        $this->assertFalse( $conditional->doUpdate(), 'If Match returns False, as there was no ETag match' );
    }

    public function testDoUpdateIfUnmodifiedReturnsFalse()
    {
        $ifUnmodifiedSince = strtotime('1 March 2013');
        $updated_at = strtotime('2 March 2013');

        $request = m::mock('Fideloper\ConditionalRequest\Request\RequestInterface');
        $request->shouldReceive('getIfMatch')->once()->andReturn( null );
        $request->shouldReceive('getIfUnmodifiedSince')->once()->andReturn( new DateTime('@'.$ifUnmodifiedSince) );
        $request->shouldReceive('attemptedEtag')->once()->andReturn( false );

        $conditional = new Conditional( $request );
        $conditional->setLastModified( new DateTime('@'.$updated_at), 'If Unmodified Since returns False, as it was modified since date given' );

        // False - don't do update
        $this->assertFalse( $conditional->doUpdate() );
    }

    public function testDoUpdateIfMatchReturnsTrue()
    {
        $request = m::mock('Fideloper\ConditionalRequest\Request\RequestInterface');
        $request->shouldReceive('getIfMatch')->once()->andReturn( '1234' );
        $request->shouldReceive('getIfUnmodifiedSince')->once()->andReturn( null ); // Still asks for this if ETag is false

        $conditional = new Conditional( $request );
        $conditional->setEtag('1234'); // This will match requests ETag

        // True - do update
        $this->assertTrue( $conditional->doUpdate(), 'If Match returns True, as there was an ETag match' );
    }

    public function testDoUpdateIfUnmodifiedReturnsTrue()
    {
        $ifUnmodifiedSince = strtotime('2 March 2013');
        $updated_at = strtotime('1 March 2013');

        $request = m::mock('Fideloper\ConditionalRequest\Request\RequestInterface');
        $request->shouldReceive('getIfMatch')->once()->andReturn( null );
        $request->shouldReceive('getIfUnmodifiedSince')->once()->andReturn( new DateTime('@'.$ifUnmodifiedSince) );
        $request->shouldReceive('attemptedEtag')->once()->andReturn( false );

        $conditional = new Conditional( $request );
        $conditional->setLastModified( new DateTime('@'.$updated_at), 'If Unmodified Since returns True, as it was not modified since date given' );

        // True - do update
        $this->assertTrue( $conditional->doUpdate() );
    }

}