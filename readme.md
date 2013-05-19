# Conditional Requests

## The Situation
Frameworks don't often give you tools to control HTTP caching mechanisms, such as setting ETags or Last-Modified dates.

## Goals
This package aims to help you with conditional HTTP requests, using [validation caching](http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html#sec13.3) mechanisms

1. Allow Validation Caching (using `ETag`s with `If-Match`, `If-None-Match` as well as `Last-Modified` with `If-Modified-Since`, `If-Unmodified-Since` headers)
2. Help developers learn about HTTP and Caching, a topic which is often ignored

## Installation
[![Build Status](https://travis-ci.org/fideloper/ConditionalRequest.png?branch=master)](https://travis-ci.org/fideloper/ConditionalRequest)

This is a Composer package, available on Packagist.

To install it, edit your composer.json file and add:

```json
{
    "require": {
        "fideloper/conditionalrequest": "dev-master"
    }
}
```

The default install will include some Symfony and Laravel (Illuminate) libraries. However these don't necessarily need to be used.

To install:

    $ composer install

To install with Phpunit and Mockery to hack on:

    $ composer install --dev