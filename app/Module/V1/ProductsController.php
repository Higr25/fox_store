<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Http\ApiRequest;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Facade\ProductsPriceChangeFacade;
use App\Domain\Api\Facade\UsersFacade;
use App\Domain\Api\Response\ProductResDto;
use App\Domain\Api\Response\UserResDto;
use App\Domain\ProductPriceChange\ProductPriceChange;
use App\Model\Utils\Caster;

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
	 *      @Apitte\RequestParameter(name="product_id", type="it", in="query", required=false, description="ID of the product to search in history for"),
	 * 		@Apitte\RequestParameter(name="before", type="string", in="query", required=false, description="String in DateTime format Y-m-d H:i:s to set as maximum date and time to which search history"),
	 *      @Apitte\RequestParameter(name="after", type="string", in="query", required=false, description="String in DateTime format Y-m-d H:i:s to set as minimum date and time from which search history"),
	 * })
	 * @return ProductResDto[]
	 */
	public function history(ApiRequest $request): array
	{
		$afterString = $request->getParameter('after')
			? str_replace('T', ' ', $request->getParameter('after'))
			: null;
		
		$beforeString = $request->getParameter('before')
			? str_replace('T', ' ', $request->getParameter('before'))
			: null;

		$cleanParams = [
			'product_id' => (int)$request->getParameter('product_id'),
			'name' => $request->getParameter('name'),
			'before' => \DateTime::createFromFormat('Y-m-d H:i:s', $beforeString ?? '') ?: null,
			'after' => \DateTime::createFromFormat('Y-m-d H:i:s', $afterString ?? '') ?: null
		];


		return $this->priceChangeFacade->findBy($cleanParams);
	}

}
