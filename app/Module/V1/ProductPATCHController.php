<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Facade\ProductsPriceChangeFacade;
use App\Domain\Api\Request\UpdateProductRequest;
use App\Domain\Api\Response\ProductResponse;
use Nette\Http\IResponse;
use OpenApi\Annotations as OA;

/**
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class ProductPATCHController extends BaseV1Controller
{

	public function __construct(
		private ProductsFacade            $productsFacade,
		private ProductsPriceChangeFacade $priceChangeFacade,
	){}

	/**
	 * @Apitte\OpenApi("
	 *   summary: Update product in store.
	 * ")
	 * @Apitte\Path("/{id}/edit")
	 * @Apitte\Method("PATCH")
	 * @Apitte\RequestParameters({
	 * 		@Apitte\RequestParameter(name="id", type="int", in="path", required=TRUE, description="ID of product to edit.")
	 * })
	 * @Apitte\RequestBody(entity="App\Domain\Api\Request\UpdateProductRequest")
	 * @Apitte\Responses({
	 *     @Apitte\Response(description="Updated Product", code=200, entity="App\Domain\Api\Response\ProductResponse"),
	 * 	   @Apitte\Response(description="Product not found", code=404)
	 * })
	 * @param ApiRequest $request
	 */

	public function index(ApiRequest $request): ?ProductResponse
	{
		/** @var UpdateProductRequest $dto */
		$dto = $request->getParsedBody();
		$id = (int)$request->getParameter('id');

		$product = $this->productsFacade->findOneBy(['id' => $id]);
		if ($product === null) {
			throw ValidationException::create()
				->withCode(404)
				->withMessage('Product not found');
		}

		$this->productsFacade->update($id, $dto);

		if ($dto->price !== null) {
			$this->priceChangeFacade->logChange($product->id, $product->price, $dto->price);
		}

		return $this->productsFacade->findOneBy(['id' => $id]);
	}
}
