<?php namespace Fideloper\ConditionalRequest\Resource;

interface ResourceInterface {

    /**
    * Retrieve ETag representing this resource
    *
    * @return string    The Entity Tag generated for the resource
    */
    public function getEtag($regen=false);

    /**
    * Retrieve last updated date for entity
    *
    * @return DateTime
    */
    public function getLastModified();
}