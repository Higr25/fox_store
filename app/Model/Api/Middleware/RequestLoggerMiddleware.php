<?php declare(strict_types=1);

namespace App\Model\Api\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tracy\ILogger;
use Contributte\Middlewares\IMiddleware;
use Psr\Log\LoggerInterface;

class RequestLoggerMiddleware implements IMiddleware
{
	private ILogger $logger;
	
	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}
	
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		$this->logRequestDetails();
		
		return $next($request, $response);
	}
	
	private function logRequestDetails(): void
	{
		// TODO: make logger work with all levels, not just ERROR
		$this->logger->log('Request received', ILogger::WARNING);
	}
}
