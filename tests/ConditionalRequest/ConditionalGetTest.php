<?php

use Mockery as m;
use Fideloper\ConditionalRequest\Conditional;

class ConditionalGetTest extends PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetEtagWrongThrowsException()
    {
        $request = m::mock('Fideloper\ConditionalRequest\Request\RequestInterface');
        $conditional = new Conditional( $request );

        // Expects a string or array
        $conditional->setEtag(123);
    }

    public function testDoGetIfNoneMatchReturnsFalse()
    {
        $request = m::mock('Fideloper\ConditionalRequest\Request\RequestInterface');
        $request->shouldReceive('getIfNoneMatch')->once()->andReturn( array('1234', 'abcd') );

        $conditional = new Conditional( $request );
        $conditional->setEtag('1234'); // This will match requests ETag

        // False - don't do get
        $this->assertFalse( $conditional->doGet(), 'If None Match returns False, as there was a match' );
    }

    public function testDoGetIfModifiedSinceReturnsFalse()
    {
        $ifModifiedSince = strtotime('2 March 2013');
        $updated_at = strtotime('1 March 2013');

        $request = m::mock('Fideloper\ConditionalRequest\Request\RequestInterface');
        $request->shouldReceive('getIfNoneMatch')->once()->andReturn( array() );
        $request->shouldReceive('getIfModifiedSince')->once()->andReturn( new DateTime('@'.$ifModifiedSince) );
        $request->shouldReceive('attemptedEtag')->once()->andReturn( false );

        $conditional = new Conditional( $request );
        $conditional->setLastModified( new DateTime('@'.$updated_at), 'If Modified Since returns False, as it was not modified since date given' ); // Updated before ifModifiedSince

        // False - don't do get
        $this->assertFalse( $conditional->doGet() );
    }

    public function testDoGetIfNoneMatchReturnsTrue()
    {
        $request = m::mock('Fideloper\ConditionalRequest\Request\RequestInterface');
        $request->shouldReceive('getIfNoneMatch')->once()->andReturn( array('1234', 'abcd') );
        $request->shouldReceive('getIfModifiedSince')->once()->andReturn( null );

        $conditional = new Conditional( $request );
        $conditional->setEtag('aaaa'); // This will NOT match requests ETag, nor attempts ifModifiedSince

        // True - do get
        $this->assertTrue( $conditional->doGet(), 'If None Match returns True, as there was no match' );
    }

    public function testDoGetIfModifiedSinceReturnsTrue()
    {
        $ifModifiedSince = strtotime('1 March 2013');
        $updated_at = strtotime('2 March 2013');

        $request = m::mock('Fideloper\ConditionalRequest\Request\RequestInterface');
        $request->shouldReceive('getIfNoneMatch')->once()->andReturn( array() );
        $request->shouldReceive('getIfModifiedSince')->once()->andReturn( new DateTime('@'.$ifModifiedSince) );
        $request->shouldReceive('attemptedEtag')->once()->andReturn( false );

        $conditional = new Conditional( $request );
        $conditional->setLastModified( new DateTime('@'.$updated_at) ); // Updated after ifModifiedSince

        $this->assertTrue( $conditional->doGet(), 'If Modified Since returns True, as it was modified since date given' );
    }

    public function testDoGetReturnsTrueBecauseEtagAttemptedButInvalid()
    {
        $ifModifiedSince = strtotime('2 March 2013');
        $updated_at = strtotime('1 March 2013');

        $request = m::mock('Fideloper\ConditionalRequest\Request\RequestInterface');
        $request->shouldReceive('getIfNoneMatch')->once()->andReturn( array() );
        $request->shouldReceive('getIfModifiedSince')->once()->andReturn( new DateTime('@'.$updated_at) );
        $request->shouldReceive('attemptedEtag')->once()->andReturn( true );

        $conditional = new Conditional( $request );
        $conditional->setLastModified( new DateTime('@'.$updated_at) ); // Updated before ifModifiedSince

        $this->assertTrue( $conditional->doGet(), 'ETag and If Modified Since both used. ETag has no match, but we still ignore If Modified Since' );
    }

}