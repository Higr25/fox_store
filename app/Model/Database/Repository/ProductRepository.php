<?php declare(strict_types = 1);

namespace App\Model\Database\Repository;

use App\Model\Database\Entity\Product;

/** @extends AbstractRepository<Product> */
class ProductRepository extends AbstractRepository
{

	public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
	{
		$query = $this->createQueryBuilder('product')
			->andWhere('product.active = 1');

		if ($criteria['name'] !== null) {
			$query->andWhere('product.name LIKE :name')
				->setParameter('name', '%' . $criteria['name'] . '%');
		}

		if ($criteria['stock_min'] !== null) {
			$query->andWhere('product.stock >= :stock_min')
				->setParameter('stock_min', $criteria['stock_min']);
		}

		if ($criteria['stock_max'] !== null) {
			$query->andWhere('product.stock <= :stock_max')
				->setParameter('stock_max', $criteria['stock_max']);
		}

		if ($orderBy !== null) {
			foreach ($orderBy as $field => $direction) {
				$query->addOrderBy('product.' . $field, $direction);
			}
		}

		return $query->getQuery()->getResult();
	}

}
