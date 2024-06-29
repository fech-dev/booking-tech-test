<?php

use App\Http\Middleware\VerifyApiToken;
use Illuminate\Http\Request;

describe('Invalid API Key', function () {
    it('should return 401 response if request does not have Authorization header', function () {
        $request = Request::create('/test', 'GET');
        $middleware = new VerifyApiToken();

        $response = $middleware->handle($request, fn () => response());
        expect($response->getStatusCode())->toBe(401);
    });

    it('should return 401 response if invalid key is given', function () {
        $request = Request::create('/test', 'GET');
        $request->headers->set('Authorization', 'invalidToken');
        $middleware = new VerifyApiToken();

        $response = $middleware->handle($request, fn () => response());
        expect($response->getStatusCode())->toBe(401);
    });
});

it('request is allowed if API key is correct', function () {
    $request = Request::create('/test', 'GET');
    $request->headers->set('Authorization', config('app.api_key'));
    $middleware = new VerifyApiToken();

    $response = $middleware->handle($request, fn () => response('ok'));
    expect($response->getStatusCode())->toBe(200);
});
