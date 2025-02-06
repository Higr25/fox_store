<?php

namespace App\Model\Api\Validator;

use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\RequestParameters;
use App\Domain\Api\Request\DateTimeStringQuery;
use App\Domain\Api\Request\ProductNameQuery;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Exception\Api\ValidationException;
use App\Model\Utils\Strings;
use Doctrine\Common\Annotations\AnnotationReader;
use OpenApi\Annotations\Schema;
use ReflectionClass;

class QueryValidator
{
	private AnnotationReader $annotationReader;
	
	public function __construct(AnnotationReader $annotationReader)
	{
		$this->annotationReader = $annotationReader;
	}
	
	public function validateQuery(ApiRequest $request, $endpoint): void
	{
		$parametersAnnotation = $this->getParametersAnnotation($endpoint);
		if (!$parametersAnnotation) {
			return;
		}
		
		foreach ($parametersAnnotation->getParameters() as $parameter) {
			$this->validateParameter($request, $parameter);
		}
	}
	
	private function getParametersAnnotation($endpoint): ?RequestParameters
	{
		$controller = $endpoint->getHandler();
		$reflectionMethod = new \ReflectionMethod($controller->getClass(), $controller->getMethod());
		$annotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);
		
		foreach ($annotations as $annotation) {
			if ($annotation instanceof RequestParameters) {
				return $annotation;
			}
		}
		
		return null;
	}
	
	private function validateParameter(ApiRequest $request, RequestParameter $parameter): void
	{
		$type = $parameter->getType();
		$value = $request->getParameter($parameter->getName());
		
		switch ($type) {
			case 'ProductNameQuery':
				$this->validateProductName($value, $parameter);
				break;
			case 'DateTimeStringQuery':
				$this->validateDateTime($value, $parameter);
				break;
		}
	}
	
	private function validateProductName(?string $value, RequestParameter $parameter): void
	{
		if (!$value) {
			return;
		}
		
		$maxLength = $this->getMaxLengthFromSchema(ProductNameQuery::class);
		if ($maxLength && strlen($value) > $maxLength) {
			throw ValidationException::create()
				->withMessage(Strings::stringError($parameter->getName(), $value))
				->withFields([$parameter->getName()]);
		}
	}
	
	private function validateDateTime(?string $value, RequestParameter $parameter): void
	{
		if (!$value) {
			return;
		}
		
		$pattern = $this->getDateTimePatternFromSchema(DateTimeStringQuery::class);
		if ($pattern && !preg_match($pattern, $value)) {
			throw ValidationException::create()
				->withMessage(Strings::dateTimeError($parameter->getName(), $value))
				->withFields([$parameter->getName()]);
		}
	}
	
	private function getDateTimePatternFromSchema(string $class): ?string
	{
		return $this->getSchemaPattern($class, 'datetime');
	}
	
	private function getMaxLengthFromSchema(string $class): ?int
	{
		return $this->getSchemaPattern($class, 'name', 'maxLength');
	}
	
	private function getSchemaPattern(string $class, string $propertyName, string $annotationKey = 'pattern')
	{
		$reflectionProperty = (new ReflectionClass($class))->getProperty($propertyName);
		$annotations = $this->annotationReader->getPropertyAnnotations($reflectionProperty);
		
		foreach ($annotations as $annotation) {
			if ($annotation instanceof Schema) {
				return $annotation->{$annotationKey} ?? null;
			}
		}
		
		return null;
	}
}
