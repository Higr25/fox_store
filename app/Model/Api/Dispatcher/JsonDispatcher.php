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
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
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

	protected function transformRequest(ApiRequest $request): ApiRequest
	{
		/** @var Endpoint|null $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);
		if ($endpoint === null) {
			return $request;
		}

		$this->queryValidator->validateQuery($request, $endpoint);

		/** @var EndpointRequestBody|null $requestBody */
		$requestBody = $endpoint->getRequestBody();
		if ($requestBody === null || $requestBody->getEntity() === null) {
			return $request;
		}

		try {
			/** @var object $dto */
			$dto = $this->serializer->deserialize(
				$request->getBody()->getContents(),
				$requestBody->getEntity(),
				'json',
				['allow_extra_attributes' => false]
			);

			$request = $request->withParsedBody($dto);

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

		} catch (NotNormalizableValueException $e) {
			throw ValidationException::create()
				->withMessage("Invalid data type in request body");
		} catch (ExtraAttributesException $e) {
			throw ValidationException::create()
				->withMessage($e->getMessage());
		}

		return $request;
	}

	protected function transformResponse(mixed $data, ApiResponse $response): ApiResponse
	{
		$response = $response->withStatus(200)
			->withHeader('Content-Type', 'application/json; charset=utf-8');

		$serialized = $this->serializer->serialize($data, 'json', [
			'json_encode_options' => JSON_UNESCAPED_UNICODE,
		]);

		$response->getBody()->write($serialized);

		return $response;
	}
}
