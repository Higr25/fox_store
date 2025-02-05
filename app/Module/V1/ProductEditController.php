<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\Api\ValidationException;
use Apitte\Core\Http\ApiRequest;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Facade\ProductsPriceChangeFacade;
use App\Domain\Api\Request\ProductPriceChangeReqDto;
use App\Domain\Api\Request\UpdateProductReqDto;
use App\Domain\Api\Response\ProductPriceChangeResDto;
use App\Domain\Api\Response\ProductResDto;
use App\Model\Exception\LogicException;
use Nette\Http\IResponse;

/**
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class ProductEditController extends BaseV1Controller
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
		
		$this->validate($dto);
		
		try {
			$product = $this->productsFacade->findOneBy(['id' => $id]);
			$this->productsFacade->update($id, $dto);
			
			$ppc_dto = new ProductPriceChangeReqDto();
			$ppc_dto->product_id = $product->id;
			$ppc_dto->old_price = $product->price;
			$ppc_dto->new_price = $dto->price;
			
			$this->priceChangeFacade->create($ppc_dto);
			
			return $this->productsFacade->findOneBy(['id' => $id]);
		} catch (\Exception $e) {
			throw ServerErrorException::create()
				->withMessage('Cannot edit product')
				->withPrevious($e);
		}
	}

	private function validate(UpdateProductReqDto $dto)
	{
		if ($dto->stock && $dto->changeStock) {
			throw ValidationException::create()
				->withCode(IResponse::S400_BadRequest)
				->withMessage('Cannot input both stock & change_stock parameters at once.');
		}
	}
}
