<?php namespace Fideloper\ConditionalRequest\Resource;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model implements ResourceInterface  {

    protected $etag;

    /**
    * Retrieve ETag for single resource
    *
    * @return string ETag for resource
    */
    public function getEtag($regen=false)
    {
        if ( $this->exists && ($this->etag === null || $regen === true)  )
        {
            $this->etag = $this->generateEtag();
        }

        return $this->etag;
    }

    /**
    * Generate ETag for single resource
    *
    * @return string ETag, using md5
    */
    protected function generateEtag()
    {
        $etag = $this->getTable() . $this->getKey();

        // Throw exception if not using timestamps?
        if ( $this->usesTimestamps() )
        {
            $datetime = $this->updated_at;

            if ( $datetime instanceof \DateTime )
            {
                $datetime = $this->fromDateTime($datetime);
            }

            $etag .= $datetime;

        }

        return md5( $etag );
    }

    /**
    * Return last updated date
    *
    * @return DateTime   Date and Time resource was last updated
    */
    public function getLastModified()
    {
        if ( ! $this->usesTimestamps() )
        {
            return false; // Throw Exception?
        }

        if( is_string($this->updated_at) )
        {
            return new \DateTime( '@'.strtotime($this->updated_at) );
        }

        return $this->updated_at;
    }

}