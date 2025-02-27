<?php

namespace App\Model\Api\Middleware;

use Contributte\Middlewares\IMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tracy\ILogger;

class JsonValidatorMiddleware implements IMiddleware
{

	public function __construct(
		private ILogger $logger
	){}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		$body = (string) $request->getBody();
		if ($body === '') {
			return $next($request, $response);
		}

		try {
			json_decode($body, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			$this->logger->log('Invalid JSON format.',ILogger::EXCEPTION);
			$response = $response->withStatus(400);
			$response->getBody()->write('Invalid JSON format.');
			return $response;
		}

		$request->getBody()->rewind();

		return $next($request, $response);
	}
}
