<?php declare(strict_types = 1);

namespace App\Module;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\OpenApi\ISchemaBuilder;
use App\Module\V1\BaseV1Controller;
use Psr\Http\Message\ResponseInterface;

/**
 * @Apitte\Path("/openapi")
 * @Apitte\Tag("OpenApi")
 */
class OpenApiController extends BaseV1Controller
{

	private ISchemaBuilder $schemaBuilder;

	public function __construct(ISchemaBuilder $schemaBuilder)
	{
		$this->schemaBuilder = $schemaBuilder;
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: Get OpenAPI definition.
	 * ")
	 * @Apitte\Path("/schema")
	 * @Apitte\Method("GET")
	 */
	public function meta(ApiRequest $request, ApiResponse $response): ResponseInterface
	{
		return $response
			->withAddedHeader('Access-Control-Allow-Origin', '*')
			->writeJsonBody(
				$this->schemaBuilder->build()->toArray()
			);
	}

}
