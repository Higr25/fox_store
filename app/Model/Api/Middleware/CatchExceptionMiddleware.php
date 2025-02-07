<?php

declare(strict_types=1);

namespace App\Model\Api\Middleware;

use Apitte\Core\Exception\Api\ClientErrorException;
use Contributte\Middlewares\IMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class CatchExceptionMiddleware implements IMiddleware
{
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		try {
			return $next($request, $response);
		} catch (Throwable $e) {
			return $this->handleException($response, $e);
		}
	}

	private function handleException(ResponseInterface $response, Throwable $e): ResponseInterface
	{
		$statusCode = 500;
		$message = 'Internal server error';

		if ($e instanceof ClientErrorException) {
			$statusCode = 400;
			$message = $e->getMessage();
		}

		$errorData = [
			'error' => true,
			'message' => $message,
			'code' => $statusCode,
		];

		$jsonResponse = json_encode($errorData, JSON_THROW_ON_ERROR);

		$response = $response->withStatus($statusCode);
		$response->getBody()->write($jsonResponse);

		return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
	}
}
