<?php namespace Fideloper\ConditionalRequest\Request;

use Symfony\Component\HttpFoundation\Request;

class Symfony implements RequestInterface {

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
    * For Conditional GET
    *
    * @return array     The ETags, or empty array
    */
    public function getIfNoneMatch()
    {
        // Symfony has this convenient method for us
        // Checks for If-None-Match header
        return $this->request->getEtags();
    }

    /**
    * For Concurrency Control
    *
    * @return string|null    The single ETag provided in If-Match header, or null
    */
    public function getIfMatch()
    {
        // Returns null or value
        return $this->request->headers->get('if-match');
    }

    /**
    * For Conditional GET
    *
    * @return DateTime|Bool     DateTime or False if not parsable/present
    */
    public function getIfModifiedSince()
    {
        return $this->request->headers->getDate('if-modified-since');
    }

    /**
    * For Concurrency Control
    *
    * @return DateTime|Bool     DateTime or False if not parsable/present
    */
    public function getIfUnmodifiedSince()
    {
        return $this->request->headers->getDate('if-unmodified-since');
    }

    /**
    * If If-None-Match or If-Match was used in request
    * Whether valid or not
    *
    * @return Bool
    */
    public function attemptedEtag($header='')
    {
        switch( str_replace('_', '-', strtolower($header)) )
        {
            case 'if-match' :
                return $this->request->headers->has('if-match');
                break;

            case 'if-none-match' :
            default :
                return $this->request->headers->has('if-none-match');
                break;
        }
    }

}