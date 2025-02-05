<?php declare(strict_types = 1);

namespace App\Module\V1;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Domain\Api\Facade\ProductsFacade;
use App\Domain\Api\Facade\UsersFacade;
use App\Domain\Api\Request\CreateProductReqDto;
use App\Domain\Api\Response\ProductResDto;
use App\Domain\Api\Response\UserResDto;
use App\Model\Utils\Caster;
use Doctrine\DBAL\Exception\DriverException;
use Nette\Http\IResponse;

/**
 * @Apitte\Path("/products")
 * @Apitte\Tag("Products")
 */
class ProductCreateController extends BaseV1Controller
{

	public function __construct(
		private ProductsFacade $productsFacade
	)
	{
	}

	/**
	 * @Apitte\OpenApi("
	 *   summary: Create product.
	 * ")
	 * @Apitte\Path("/create")
	 * @Apitte\Method("POST")
	 * @Apitte\RequestBody(entity="App\Domain\Api\Request\CreateProductReqDto")
	 */
	public function index(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		/** @var CreateProductReqDto $dto */
		$dto = $request->getParsedBody();

		try {
			$this->productsFacade->create($dto);

			return $response->withStatus(IResponse::S201_Created)
				->withHeader('Content-Type', 'application/json; charset=utf-8');
		} catch (DriverException $e) {
			throw ServerErrorException::create()
				->withMessage('Cannot create product')
				->withPrevious($e);
		}
	}

}
