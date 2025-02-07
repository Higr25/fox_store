<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Http\ApiRequest;
use App\Domain\Api\Facade\ProductsPriceChangeFacade;
use App\Domain\Api\Response\ProductPriceChangeResDto;

/**
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class PriceHistoryGETController extends BaseV1Controller
{

	public function __construct(
		private ProductsPriceChangeFacade $priceChangeFacade,
	)
	{
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: List products price history changes.
	 * ")
	 * @Apitte\Path("/price-history")
	 * @Apitte\Method("GET")
	 * @Apitte\RequestParameters({
	 *      @Apitte\RequestParameter(name="product_id", type="int", in="query", required=false, description="ID of the product to search for in history"),
	 * 		@Apitte\RequestParameter(name="before", type="DateTimeStringQuery", in="query", required=false, description="String in DateTime format Y-m-d\TH:i:s to set as maximum date and time to which search history"),
	 *      @Apitte\RequestParameter(name="after", type="DateTimeStringQuery", in="query", required=false, description="String in DateTime format Y-m-d\TH:i:s to set as minimum date and time from which search history"),
	 * })
	 * @return ProductPriceChangeResDto[]
	 */
	public function history(ApiRequest $request): array
	{
		return $this->priceChangeFacade->findBy($request->getParameters());
	}
}
