<?php declare(strict_types = 1);

namespace App\Domain\Api\Facade;

use App\Domain\Api\Response\ProductPriceChangeResponse;
use App\Model\Database\Entity\Product;
use App\Model\Database\Entity\ProductPriceChange;
use App\Model\Database\EntityManagerDecorator;
use Tracy\ILogger;

final class ProductsPriceChangeFacade
{

	public function __construct(
		private EntityManagerDecorator $em,
		private ILogger $logger
	)
	{
	}

	/**
	 * @param mixed[] $criteria
	 * @param string[] $orderBy
	 * @return ProductPriceChangeResponse[]
	 */
	public function findBy(array $criteria = [], ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
	{
		$entities = $this->em->getRepository(ProductPriceChange::class)->findBy($criteria, $orderBy, $limit, $offset);
		$result = [];

		foreach ($entities as $entity) {
			$result[] = ProductPriceChangeResponse::from($entity);
		}

		return $result;
	}

	public function logChange(int $productId, float $oldPrice, float $newPrice): void
	{
		$product = $this->em->getRepository(Product::class)->find($productId);
		if ($product === null) {
			$this->logger->log(ILogger::ERROR, 'Product not found when saving price history: ' . $productId);
			return;
		}

		$productPriceChange = new ProductPriceChange(
			$product,
			$oldPrice,
			$newPrice,
		);

		$this->em->persist($productPriceChange);
		$this->em->flush($productPriceChange);
	}
}
