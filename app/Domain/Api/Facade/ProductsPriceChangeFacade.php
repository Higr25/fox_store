<?php declare(strict_types = 1);

namespace App\Domain\Api\Facade;

use App\Domain\Api\Request\CreateProductReqDto;
use App\Domain\Api\Request\CreateUserReqDto;
use App\Domain\Api\Request\ProductPriceChangeReqDto;
use App\Domain\Api\Request\UpdateProductReqDto;
use App\Domain\Api\Response\ProductPriceChangeResDto;
use App\Domain\Api\Response\ProductResDto;
use App\Domain\Api\Response\UserResDto;
use App\Domain\Product\Product;
use App\Domain\ProductPriceChange\ProductPriceChange;
use App\Domain\User\User;
use App\Model\Database\EntityManagerDecorator;
use App\Model\Exception\Runtime\Database\EntityNotFoundException;
use App\Model\Security\Passwords;
use Doctrine\Common\Collections\Criteria;

final class ProductsPriceChangeFacade
{

	public function __construct(private EntityManagerDecorator $em)
	{
	}

	/**
	 * @param mixed[] $criteria
	 * @param string[] $orderBy
	 * @return ProductPriceChangeResDto[]
	 */
	public function findBy(array $criteria = [], ?array $orderBy = null, $limit = null, $offset = null): array
	{
		$entities = $this->em->getRepository(ProductPriceChange::class)->findBy($criteria, $orderBy, $limit, $offset);
		$result = [];

		foreach ($entities as $entity) {
			$result[] = ProductPriceChangeResDto::from($entity);
		}

		return $result;
	}

	/**
	 * @param mixed[] $criteria
	 * @param string[] $orderBy
	 */
	public function findOneBy(array $criteria, ?array $orderBy = null): ProductPriceChangeResDto
	{
		$entity = $this->em->getRepository(ProductPriceChange::class)->findOneBy($criteria, $orderBy);

		if ($entity === null) {
			throw new EntityNotFoundException();
		}

		return ProductPriceChangeResDto::from($entity);
	}
	
	public function create(ProductPriceChangeReqDto $dto): void
	{
		$entity = $this->em->getRepository(Product::class)->findOneBy(['id' => $dto->product_id]);
		
		$product = new ProductPriceChange(
			$entity,
			$dto->old_price,
			$dto->new_price,
		);
		
		$this->em->persist($product);
		$this->em->flush($product);
	}
}
