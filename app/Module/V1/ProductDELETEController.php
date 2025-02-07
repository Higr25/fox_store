<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Domain\Api\Facade\ProductsFacade;
use Doctrine\DBAL\Exception\DriverException;
use Nette\Http\IResponse;

/**
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class ProductDELETEController extends BaseV1Controller
{

	public function __construct(
		private ProductsFacade $productsFacade
	)
	{
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: Delete product.
	 * ")
	 * @Apitte\Path("/{id}/delete")
	 * @Apitte\Method("DELETE")
	 * @Apitte\RequestParameters({
	 * 		@Apitte\RequestParameter(name="id", type="int", in="path", required=TRUE, description="ID of product to delete.")
	 * })
	 */
	public function index(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$this->productsFacade->delete((int)$request->getParameter('id'));

			return $response->withStatus(IResponse::S204_NoContent);
		} catch (DriverException $e) {
			throw ServerErrorException::create()
				->withMessage('Cannot delete product')
				->withPrevious($e);
		}
	}

}
