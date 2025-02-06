<?php declare(strict_types = 1);

namespace App\Model\Api\Dispatcher;

use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Dispatcher\JsonDispatcher as ApitteJsonDispatcher;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Handler\IHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Router\IRouter;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointRequestBody;
use App\Model\Api\Validator\QueryValidator;
use Doctrine\Common\Annotations\AnnotationReader;
use Nette\Utils\Json;
use OpenApi\Annotations\Schema;
use ReflectionClass;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class JsonDispatcher extends ApitteJsonDispatcher
{

	protected SerializerInterface $serializer;

	protected ValidatorInterface $validator;
	
	protected AnnotationReader $annotationReader;
	
	protected QueryValidator $queryValidator;

	public function __construct(IRouter $router, IHandler $handler, SerializerInterface $serializer, ValidatorInterface $validator, AnnotationReader $annotationReader, QueryValidator $queryValidator)
	{
		parent::__construct($router, $handler);

		$this->serializer = $serializer;
		$this->validator = $validator;
		$this->annotationReader = $annotationReader;
		$this->queryValidator = $queryValidator;
	}

	protected function handle(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$request = $this->transformRequest($request);
			$result = $this->handler->handle($request, $response);

			// Except ResponseInterface convert all to json
			$response = !($result instanceof ApiResponse) ? $this->transformResponse($result, $response) : $result;
		} catch (ClientErrorException | ServerErrorException $e) {
			$data = [];

			if ($e->getMessage() !== '') {
				$data['message'] = $e->getMessage();
			}

			if ($e->getContext() !== null) {
				$data['context'] = $e->getContext();
			}

			if ($e->getCode() !== 0) {
				$data['code'] = $e->getCode();
			}

			$response = $response->withStatus($e->getCode() !== 0 ? $e->getCode() : 500)
				->withHeader('Content-Type', 'application/json; charset=utf-8');
			$response->getBody()->write(Json::encode($data, true, true));
		} catch (Throwable $e) {
			throw $e;
		}

		return $response;
	}

	/**
	 * Transform incoming request to request DTO, if needed.
	 */
	protected function transformRequest(ApiRequest $request): ApiRequest
	{
		// If Apitte endpoint is not provided, skip transforming.
		/** @var Endpoint|null $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);
		if ($endpoint === null) {
			return $request;
		}
		
		// Validate query parameters
		$this->queryValidator->validateQuery($request, $endpoint);

		// Get incoming request entity class, if defined. Otherwise, skip transforming.
		/** @var EndpointRequestBody|null $requestBody */
		$requestBody = $endpoint->getRequestBody();
		if ($requestBody === null || $requestBody->getEntity() === null) {
			return $request;
		}

		try {
			// Create request DTO from incoming request, using serializer.
			/** @var object $dto */
			$dto = $this->serializer->deserialize(
				$request->getBody()->getContents(),
				$requestBody->getEntity(),
				'json',
				['allow_extra_attributes' => false]
			);

			$request = $request->withParsedBody($dto);
		} catch (ExtraAttributesException $e) {
			throw ValidationException::create()
				->withMessage($e->getMessage());
		}

		// Try to validate entity only if its enabled
		$violations = $this->validator->validate($dto);

		if (count($violations) > 0) {
			$fields = [];
			foreach ($violations as $violation) {
				$fields[$violation->getPropertyPath()][] = $violation->getMessage();
			}

			throw ValidationException::create()
				->withMessage('Invalid request data')
				->withFields($fields);
		}

		return $request;
	}

	/**
	 * Transform outgoing response data to JSON, if needed.
	 */
	protected function transformResponse(mixed $data, ApiResponse $response): ApiResponse
	{
		$response = $response->withStatus(200)
			->withHeader('Content-Type', 'application/json; charset=utf-8');

		// Serialize entity with symfony/serializer to JSON
		$serialized = $this->serializer->serialize($data, 'json');

		$response->getBody()->write($serialized);

		return $response;
	}
	
	private function validateQuery(ApiRequest $request, $endpoint): void
	{
		$controller = $endpoint->getHandler();
		$reflectionMethod = new \ReflectionMethod($controller->getClass(), $controller->getMethod());
		$annotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);
		
		$parametersAnnotation = null;
		foreach ($annotations as $annotation) {
			if ($annotation instanceof RequestParameters) {
				$parametersAnnotation = $annotation;
				break;
			}
		}
		
		if ($parametersAnnotation !== null) {
			$parameters = $parametersAnnotation->getParameters(); // This will return an array of RequestParameter objects
			
			foreach ($parameters as $parameter) {
				$activeParam = $request->getParameter($parameter->getName());
				if ($parameter->getType() === 'DateTimeString' && $activeParam) {
					if (!preg_match($this->getDateTimePatternFromSchema(), $activeParam)) {
						throw ValidationException::create()
							->withMessage('Invalid query parameter '.$parameter->getName().'. (valid example: 2025-02-05T12:34:56)')
							->withFields([$parameter->getName()]);
					}
				}
			}
		}
	}

	private function getDateTimePatternFromSchema(): ?string
	{
		$reflectionClass = new ReflectionClass(DateTimeStringReqDto::class);
		
		$reflectionProperty = $reflectionClass->getProperty('datetime');
		
		$annotations = $this->annotationReader->getPropertyAnnotations($reflectionProperty);
		
		foreach ($annotations as $annotation) {
			if ($annotation instanceof Schema) {
				 return $annotation->pattern;
			}
		}

		return null;
	}

}
