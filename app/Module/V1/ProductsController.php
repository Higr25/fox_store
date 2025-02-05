<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Http\ApiRequest;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Facade\ProductsPriceChangeFacade;
use App\Domain\Api\Response\ProductPriceChangeResDto;
use App\Domain\Api\Response\ProductResDto;
use App\Model\Utils\DateTime;
use App\Model\Utils\Validators;

/**
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class ProductsController extends BaseV1Controller
{

	public function __construct(
		private ProductsFacade $productsFacade,
		private ProductsPriceChangeFacade $priceChangeFacade
	)
	{
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: List products.
	 * ")
	 * @Apitte\Path("/")
	 * @Apitte\Method("GET")
	 * @Apitte\RequestParameters({
	 *      @Apitte\RequestParameter(name="name", type="string", in="query", required=false, description="Name of the product to search for"),
	 * 		@Apitte\RequestParameter(name="stock_min", type="int", in="query", required=false, description="Minimum required stock amount"),
	 *      @Apitte\RequestParameter(name="stock_max", type="int", in="query", required=false, description="Maximum required stock amount"),
	 * })
	 * @return ProductResDto[]
	 */
	public function index(ApiRequest $request): array
	{
		return $this->productsFacade->findBy($request->getParameters());
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: List products price history changes.
	 * ")
	 * @Apitte\Path("/price-history")
	 * @Apitte\Method("GET")
	 * @Apitte\RequestParameters({
	 *      @Apitte\RequestParameter(name="product_id", type="int", in="query", required=false, description="ID of the product to search in history for"),
	 * 		@Apitte\RequestParameter(name="before", type="string", in="query", required=false, description="String in DateTime format Y-m-d H:i:s to set as maximum date and time to which search history"),
	 *      @Apitte\RequestParameter(name="after", type="string", in="query", required=false, description="String in DateTime format Y-m-d H:i:s to set as minimum date and time from which search history"),
	 * })
	 * @return ProductPriceChangeResDto[]
	 */
	public function history(ApiRequest $request): array
	{
		$this->validate($request);

		$cleanParams = [
			'product_id' => (int)$request->getParameter('product_id'),
			'before' => DateTime::createFromQueryParam($request->getParameter('before') ?? ''),
			'after' => DateTime::createFromQueryParam($request->getParameter('after') ?? ''),
		];

		return $this->priceChangeFacade->findBy($cleanParams);
	}

	private function validate(ApiRequest $request): void
	{
		$productId = $request->getParameter('product_id');
		if (isset($productId)) {
			Validators::integer($request->getParameter('product_id'));
		}

		if ($request->getParameter('before')) {
			Validators::dateTime($request->getParameter('before'));
		}

		if ($request->getParameter('before')) {
			Validators::dateTime($request->getParameter('before'));
		}
	}
}
