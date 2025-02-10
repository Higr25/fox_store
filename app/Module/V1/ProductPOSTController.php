<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Request\CreateProductRequest;
use Doctrine\DBAL\Exception\DriverException;
use Nette\Http\IResponse;
use Apitte\Core\Exception\Api\ValidationException;
use OpenApi\Attributes as OA;

/**
 * @Apitte\Path("/product")
 * @Apitte\Tag("Products")
 */
class ProductPOSTController extends BaseV1Controller
{

	public function __construct(
		private ProductsFacade $productsFacade,
	)
	{}

	/**
	 * @Apitte\OpenApi("summary: Create new product in store. Maximum name length is 50 characters.")
	 * @Apitte\Path("/create")
	 * @Apitte\Method("POST")
	 * @Apitte\RequestBody(entity="App\Domain\Api\Request\CreateProductRequest")
	 * @Apitte\Responses({
	 *      @Apitte\Response(description="Product created", code=201),
 	 *  	@Apitte\Response(description="Product Name already exists", code=409)
	 *  })
	 */
	public function index(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		/** @var CreateProductRequest $dto */
		$dto = $request->getParsedBody();

		$product = $this->productsFacade->findOneBy(['name' => $dto->name]);
		if (isset($product)) {
			throw ValidationException::create()
				->withMessage('Product with this name already exists.')
				->withCode(IResponse::S409_Conflict);
		};

		try {
			$this->productsFacade->create($dto);

			return $response->withStatus(IResponse::S201_Created);
		} catch (DriverException $e) {
			throw ServerErrorException::create()
				->withMessage('Cannot create product')
				->withPrevious($e);
		}
	}

}
