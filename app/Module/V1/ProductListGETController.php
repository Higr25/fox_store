<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Http\ApiRequest;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Facade\ProductsPriceChangeFacade;
use App\Domain\Api\Response\ProductPriceChangeResponse;
use App\Domain\Api\Response\ProductResponse;
use OpenApi\Annotations\OpenApi as OA;
use Tracy\ILogger;

/**
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class ProductListGETController extends BaseV1Controller
{

	public function __construct(
		private ProductsFacade $productsFacade,
	)
	{
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: List products in store. Add
	 * ")
	 * @Apitte\Path("/")
	 * @Apitte\Method("GET")
	 * @Apitte\RequestParameters({
	 *      @Apitte\RequestParameter(name="name", type="ProductNameQuery", in="query", required=false, description="Name of the product to search for"),
	 * 		@Apitte\RequestParameter(name="stock_min", type="int", in="query", required=false, description="Minimum required stock amount"),
	 *      @Apitte\RequestParameter(name="stock_max", type="int", in="query", required=false, description="Maximum required stock amount"),
	 * })
	 * @return ProductResponse[]
	 */
	public function index(ApiRequest $request): array
	{
		return $this->productsFacade->findBy($request->getParameters(), ['created_at' => 'desc']);
	}
}
