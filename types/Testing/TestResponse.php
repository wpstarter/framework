<?php

use WpStarter\Http\JsonResponse;
use WpStarter\Http\RedirectResponse;
use WpStarter\Http\Response;
use WpStarter\Testing\TestResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function PHPStan\Testing\assertType;

$response = TestResponse::fromBaseResponse(ws_response('Laravel', 200));
assertType(Response::class, $response->baseResponse);

$response = TestResponse::fromBaseResponse(ws_response()->redirectTo(''));
assertType(RedirectResponse::class, $response->baseResponse);

$response = TestResponse::fromBaseResponse(ws_response()->download(''));
assertType(BinaryFileResponse::class, $response->baseResponse);

$response = TestResponse::fromBaseResponse(ws_response()->json());
assertType(JsonResponse::class, $response->baseResponse);

$response = TestResponse::fromBaseResponse(ws_response()->streamDownload(fn () => 1));
assertType(StreamedResponse::class, $response->baseResponse);
