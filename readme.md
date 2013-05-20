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

## Methods Available:

<!-- a -->

1. **using** ( ResourceInterface *$resource* ) - Use ResourceInterface, which sets the ETag and/or Last Modified date automatically
1. **setEtag** ( string *$etag* )  - Set the ETag for an entity "manually"
2. **setLastModified*** ( DateTime *$lastModified* ) - Set the last modified date for an entity "manually"
3. bool **doGet** () - Determine if your app should respond to a GET request with entity (the resource) or `304 Not Modified`
4. bool **doUpate** () - Determine if your app should update the entity/resource in a PUT/POST request or respond with `412 Precondition Failed`

## Basic Usage:

1. [Laravel/Symfony](https://github.com/fideloper/ConditionalRequest/wiki/Laravel-Symfony)
2. Zend [Future]
3. "Pure" PHP [Future]

## More Usage:

**For more complete usage examples and explanation, see the [Wiki](https://github.com/fideloper/ConditionalRequest/wiki).**

## Some Explanation
There are a few types of caching:

1. In-app caching (Memcache, Redis, other memory stores)
2. HTTP caching - gateway, proxy and private (aka browsers, and similar)

Making a response (web page, api-response, etc) cachable by third-parties is part of the HTTP cache mechanisms. Which cache mechanisms you use depends on your use case.

The HTTP spec defines 2 methods of HTTP caching:

1. **Validation** - save bandwidth by not having an origin server reply with a full message body (header-only response)
2. **Expiration** - to save round-trips to the origin server - a cache can potentially serve a response directly, saving the origin server from even knowing about the request

### Validation caching
Validation Caching, done with if-* headers (mainly if-match, if-none-match, if-modified-since, if-unmodified-since) is useful for 2 things:

#### Conditional GET requests
A server can tell the request 'nothing has changed since you last checked'. The client then knows to use its last-known version of the resource (assuming the reuslt of prior requests). This is good for mobile APIs where the bandwidth of re-sending a message body can be saved via conditional requests.

A server can respond with the resource as normal, or return a `304 Not Modified` response if the resource wasn't updated since the client last asked for it.

#### Concurrency Control
In a PUT (or possibly POST) request, a server can check if the resource being updated was changed since the requester last checked (solves the [Lost Update Problem](http://www.w3.org/1999/04/Editing/)). This is good for APIs with a lot of writes (updates) to resources.

A server can response with `412 Precondition Failed` if the client requesting the update doesn't have the latest knowledge of the resource.

**This library is coded to help with Validation Cacheing.**

### Expiration caching
Expiration caching, done with Expires, Cache-Control, Last-Modified and other headers, can aid in caching a response for the next user (or even for one specific user), saving your server(s) from some traffic load