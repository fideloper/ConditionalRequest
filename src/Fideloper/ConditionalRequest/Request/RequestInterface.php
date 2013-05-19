<?php namespace Fideloper\ConditionalRequest\Request;

interface RequestInterface {

    /**
    * For Conditional GET
    *
    * @return array     The ETags, or empty array
    */
    public function getIfNoneMatch();

    /**
    * For Concurrency Control
    *
    * @return string|null    The single ETag provided in If-Match header, or null
    */
    public function getIfMatch();

    /**
    * For Conditional GET
    *
    * @return DateTime
    */
    public function getIfModifiedSince();

    /**
    * For Concurrency Control
    *
    * @return DateTime
    */
    public function getIfUnmodifiedSince();

    /**
    * If If-None-Match or If-Match was used in request
    * Whether valid or not
    *
    * @return Bool
    */
    public function attemptedEtag($header='');

}