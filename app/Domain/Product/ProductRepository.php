<?php declare(strict_types = 1);

namespace App\Domain\Product;

use App\Model\Database\Repository\AbstractRepository;

/**
// * @method Product|NULL find($id)
// * @method Product|NULL findOneBy(array $criteria, array $orderBy = NULL)
// * @method Product[] findAll()
// * @method Product[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends AbstractRepository<Product>
 */
class ProductRepository extends AbstractRepository
{

	public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
	{

		$query = $this->createQueryBuilder('product');

		if ($criteria['name']) {
			$query->andWhere('product.name LIKE :name')
				->setParameter('name', '%' . $criteria['name'] . '%');
		}

		if ($criteria['stock_min']) {
			$query->andWhere('product.stock >= :stock_min')
				->setParameter('stock_min', $criteria['stock_min']);
		}

		if ($criteria['stock_max']) {
			$query->andWhere('product.stock <= :stock_max')
				->setParameter('stock_max', $criteria['stock_max']);
		}

		return $query->getQuery()->getResult();
	}

}
