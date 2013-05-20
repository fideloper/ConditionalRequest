<?php

use Mockery as m;
use Fideloper\ConditionalRequest\Conditional;

class ConcurrencyControlTest extends PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    public function testSetEtagWrongThrowsException()
    {
        $this->assertTrue( true );
    }

}