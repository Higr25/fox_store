<?php

declare(strict_types=1);

namespace App\Model\Api\Middleware;

use Contributte\Middlewares\IMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Predis\Client as Redis;
use Tracy\ILogger;

class RateLimitMiddleware implements IMiddleware
{

	public function __construct(
		private int $limit,
		private int $window,
		private Redis $redis,
		private ILogger $logger,
	)
	{}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		if (!$ip = $request->getServerParams()['REMOTE_ADDR']) {
			$this->logger->log('Unknown IP', ILogger::WARNING);
			return $next($request, $response);
		}

		$cacheKey = "rate_limit_$ip";
		$requestCount = $this->redis->get($cacheKey);
		if ($requestCount === null) {
			$this->redis->setex($cacheKey, $this->window, 0);
			$requestCount = 0;
		}

		if ($requestCount >= $this->limit) {
			$response = $response->withStatus(429);
			$response->getBody()->write('Rate limit exceeded. Try again later.');
			return $response;
		}

		$this->redis->incr($cacheKey);

		return $next($request, $response);
	}
}
