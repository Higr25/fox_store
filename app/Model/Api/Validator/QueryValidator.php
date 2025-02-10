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
use ReflectionException;

class QueryValidator
{
	public function __construct(private AnnotationReader $annotationReader) {}

	public function validateQuery(ApiRequest $request, Endpoint $endpoint): void
	{
		foreach ($this->getQueryParameters($endpoint) as $parameter) {
			$name = $parameter->getName();
			$value = $request->getParameter($name);

			if ($parameter->isRequired() && $value === null) {
				throw ValidationException::create()->withMessage("Missing required query parameter: $name");
			}

			if ($value !== null) {
				match ($parameter->getType()) {
					'ProductNameQuery' => $this->validateProductName($value, $parameter),
					'DateTimeStringQuery' => $this->validateDateTime($value, $parameter),
					'int' => $this->validateInt($value, $parameter),
					default => null,
				};
			}
		}
	}

	/**
	 * @param Endpoint $endpoint
	 * @return RequestParameter[]
	 * @throws ReflectionException
	 */
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

	private function validateProductName(string $value, RequestParameter $parameter): void
	{
		$maxLength = $this->getSchemaProperty(ProductNameQuery::class, 'maxLength');
		if ($maxLength !== null && strlen($value) > $maxLength) {
			$this->throwValidationException($parameter, $value);
		}
	}

	private function validateDateTime(string $value, RequestParameter $parameter): void
	{
		$pattern = $this->getSchemaProperty(DateTimeStringQuery::class, 'pattern');
		if ($pattern !== null && preg_match($pattern, $value) !== 1) {
			$this->throwValidationException($parameter, $value);
		}
	}

	private function validateInt(mixed $value, RequestParameter $parameter): void
	{
		if (!is_numeric($value) || (int)$value != $value) {
			$this->throwValidationException($parameter, $value);
		}
	}


	/**
	 * @param string $class
	 * @param string $annotationKey
	 * @return string|null
	 * @throws ReflectionException
	 */
	private function getSchemaProperty(string $class, string $annotationKey): ?string
	{
		if (!class_exists($class)) {
			throw new \InvalidArgumentException("Class '$class' does not exist.");
		}

		$reflectionClass = new \ReflectionClass($class);

		foreach ($reflectionClass->getProperties() as $property) {
			$annotations = $this->annotationReader->getPropertyAnnotations($property);

			foreach ($annotations as $annotation) {
				if ($annotation instanceof Schema && property_exists($annotation, $annotationKey)) {
					return $annotation->{$annotationKey};
				}
			}
		}

		return null;
	}


	private function throwValidationException(RequestParameter $parameter, string $value): void
	{
		throw ValidationException::create()
			->withMessage('Invalid query parameter '. $parameter->getName() . ': '. $value);
	}
}
