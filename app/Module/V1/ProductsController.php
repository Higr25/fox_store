<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Http\ApiRequest;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Facade\UsersFacade;
use App\Domain\Api\Response\ProductResDto;
use App\Domain\Api\Response\UserResDto;
use App\Model\Utils\Caster;

/**
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class ProductsController extends BaseV1Controller
{

	public function __construct(
		private ProductsFacade $productsFacade
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

}
