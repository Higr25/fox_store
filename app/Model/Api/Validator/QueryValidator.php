<?php

namespace App\Model\Api\Validator;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Schema\Endpoint;
use App\Domain\Api\Request\DateTimeStringQuery;
use App\Domain\Api\Request\ProductNameQuery;
use Doctrine\Common\Annotations\AnnotationReader;
use OpenApi\Annotations\Schema;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\RequestParameters;

class QueryValidator
{
	public function __construct(private AnnotationReader $annotationReader) {}

	public function validateQuery(ApiRequest $request, Endpoint $endpoint): void
	{
		foreach ($this->getQueryParameters($endpoint) as $parameter) {
			$this->validateParameter($request, $parameter);
		}
	}

	private function getQueryParameters(Endpoint $endpoint): array
	{
		$controller = $endpoint->getHandler();
		$reflectionMethod = new \ReflectionMethod($controller->getClass(), $controller->getMethod());
		$annotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);

		foreach ($annotations as $annotation) {
			if ($annotation instanceof RequestParameters) {
				return $annotation->getParameters();
			}
		}

		return [];
	}

	private function validateParameter(ApiRequest $request, RequestParameter $parameter): void
	{
		if ($parameter->isRequired() && !$request->getParameter($parameter->getName())) {
			throw ValidationException::create()
				->withMessage('Missing required query parameter: ' . $parameter->getName());
		}

		$value = $request->getParameter($parameter->getName());
		if (empty($value)) {
			return;
		}

		match ($parameter->getType()) {
			'ProductNameQuery' => $this->validateProductName($value, $parameter),
			'DateTimeStringQuery' => $this->validateDateTime($value, $parameter),
			'int' => $this->validateInt($value, $parameter),
			default => null,
		};
	}

	private function validateProductName(string $value, RequestParameter $parameter): void
	{
		$maxLength = $this->getSchemaProperty(ProductNameQuery::class, 'maxLength');
		if ($maxLength && strlen($value) > $maxLength) {
			$this->throwValidationException($parameter, $value);
		}
	}

	private function validateDateTime(string $value, RequestParameter $parameter): void
	{
		$pattern = $this->getSchemaProperty(DateTimeStringQuery::class, 'pattern');
		if ($pattern && !preg_match($pattern, $value)) {
			$this->throwValidationException($parameter, $value);
		}
	}

	private function validateInt($value, RequestParameter $parameter): void
	{
		if (!is_numeric($value) || (int)$value != $value) {
			$this->throwValidationException($parameter, $value);
		}
	}

	private function getSchemaProperty(string $class, string $annotationKey): ?string
	{
		$reflectionClass = new \ReflectionClass($class);
		foreach ($reflectionClass->getProperties() as $property) {
			$annotations = $this->annotationReader->getPropertyAnnotations($property);
			foreach ($annotations as $annotation) {
				if ($annotation instanceof Schema) {
					return $annotation->{$annotationKey} ?? null;
				}
			}
		}
		return null;
	}

	private function throwValidationException(RequestParameter $parameter, $value): void
	{
		throw ValidationException::create()
			->withMessage('Invalid query parameter '. $parameter->getName() . ': '. $value);
	}
}
