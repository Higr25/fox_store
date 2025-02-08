<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Http\ApiRequest;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Facade\ProductsPriceChangeFacade;
use App\Domain\Api\Request\UpdateProductReqDto;
use App\Domain\Api\Response\ProductResDto;
use Nette\Http\IResponse;

/**
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class ProductPATCHController extends BaseV1Controller
{

	public function __construct(
		private ProductsFacade $productsFacade,
		private ProductsPriceChangeFacade $priceChangeFacade,
	){}

	/**
	 * @Apitte\OpenApi("
	 *   summary: Edit product.
	 * ")
	 * @Apitte\Path("/{id}/edit")
	 * @Apitte\Method("PATCH")
	 * @Apitte\RequestParameters({
	 * 		@Apitte\RequestParameter(name="id", type="int", in="path", required=TRUE, description="ID of product to edit.")
	 * })
	 * @Apitte\RequestBody(entity="App\Domain\Api\Request\UpdateProductReqDto")
	 * @param ApiRequest $request
	 * @return ProductResDto
	 */
	public function index(ApiRequest $request): ProductResDto
	{
		/** @var UpdateProductReqDto $dto */
		$dto = $request->getParsedBody();
		$id = (int)$request->getParameter('id');

		$product = $this->productsFacade->findOneBy(['id' => $id]);
		if (!$product) {
			throw ValidationException::create()
				->withCode(404)
				->withMessage('Product not found');
		}

		$this->productsFacade->update($id, $dto);

		if ($dto->price) {
			$this->priceChangeFacade->logChange($product->id, $product->price, $dto->price);
		}

		return $this->productsFacade->findOneBy(['id' => $id]);
	}
}
