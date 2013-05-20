<?php namespace Fideloper\ConditionalRequest;

use Fideloper\ConditionalRequest\Request\RequestInterface;
use Fideloper\ConditionalRequest\Resource\ResourceInterface;

class Conditional {

    protected $request;
    protected $resource;

    protected $etag;
    protected $lastModified;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
    * Set resource (optionally used)
    *
    * @param ResourceInterface  Class implemeting ResourceInterface
    * @return Conditional  Otherwise known as $this
    */
    public function using(ResourceInterface $resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
    * Set ETag "manually" (if no resource passed)
    *
    * @param string         An ETag
    * @return Conditional   Otherwise known as $this
    */
    public function setEtag($etag)
    {
        if( is_string($etag) === false )
        {
            throw new \InvalidArgumentException('ETag must be a string');
        }

        $this->etag = $etag;

        return $this;
    }

    /**
    * Set Last Modified date of entity
    *
    * @param DateTime  DateTime object representing last update date
    * @return Conditional  Otherwise known as $this
    */
    public function setLastModified(\DateTime $lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
    * Retreive ETag, prefering resource over manually set etag
    *
    * @return string    The entities tag
    */
    protected function getEtag()
    {
        if( is_null($this->resource) )
        {
            return $this->etag;
        }

        return $this->resource->getEtag();
    }

    /**
    * Retreive Last Modified date, prefering resource over manually set date
    *
    * @return DateTime    The entities last updated date
    */
    protected function getLastModified()
    {
        if( is_null($this->resource) )
        {
            return $this->lastModified;
        }

        return $this->resource->getLastModified();
    }

    /**
    * Determine if resource was modified since the
    * requester's last check
    *
    * For Conditional GET:
    * Uses `If-None-Match` and/or `If-Modified-Since` header.
    * ETag take priority over Last Modified for Validation.
    *
    * @todo  handle `If-None-Match: *`
    * @return bool
    */
    public function doGet()
    {
        // First, ETag Validation
        $etags = $this->request->getIfNoneMatch();

        foreach( $etags as $etag )
        {
            if( $etag === $this->getEtag() )
            {
                return false;
            }
        }

        // Second, Modification Date Validation
        $ifModifiedSince = $this->request->getIfModifiedSince();

        if( $ifModifiedSince && $this->request->attemptedEtag('if-none-match') === false )
        {
            if( $ifModifiedSince->getTimestamp() >= $this->getLastModified()->getTimestamp() )
            {
                return false;
            }
        }

        return true;
    }

    /**
    * Determine if resource was NOT modified
    * since requester's last check
    *
    * For Concurrency Control (Lost Update Problem)
    * Uses `If-Match` and/or `If-Unmodified-Since`
    */
    public function doUpdate()
    {
        // First, ETag Validation
        $etag = $this->request->getIfMatch();

        if( $etag )
        {
            if( $etag !== $this->getEtag() )
            {
                return false;
            }
        }

        // Second, Modification Date Validation
        $ifUnmodifiedSince = $this->request->getIfUnmodifiedSince();

        if( $ifUnmodifiedSince && $this->request->attemptedEtag('if-match') === false )
        {
            if( $ifUnmodifiedSince->getTimestamp() < $this->getLastModified()->getTimestamp() )
            {
                return false;
            }
        }

        return true;
    }

}