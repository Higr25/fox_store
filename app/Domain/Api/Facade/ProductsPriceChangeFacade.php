<?php declare(strict_types = 1);

namespace App\Domain\Api\Facade;

use App\Domain\Api\Response\ProductPriceChangeResDto;
use App\Model\Database\Entity\Product;
use App\Model\Database\Entity\ProductPriceChange;
use App\Model\Database\EntityManagerDecorator;
use App\Model\Exception\Runtime\Database\EntityNotFoundException;

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

	public function logChange(int $productId, float $oldPrice, float $newPrice): void
	{
		$product = $this->em->getRepository(Product::class)->find($productId);
		$productPriceChange = new ProductPriceChange(
			$product,
			$oldPrice,
			$newPrice,
		);

		$this->em->persist($productPriceChange);
		$this->em->flush($productPriceChange);
	}
}
