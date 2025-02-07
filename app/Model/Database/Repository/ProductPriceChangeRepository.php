<?php declare(strict_types = 1);

namespace App\Model\Database\Repository;

use App\Model\Database\Entity\ProductPriceChange;

/**
// * @method Product|NULL find($id)
// * @method Product|NULL findOneBy(array $criteria, array $orderBy = NULL)
// * @method Product[] findAll()
// * @method Product[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends AbstractRepository<ProductPriceChange>
 */
class ProductPriceChangeRepository extends AbstractRepository
{

	public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
	{
		$query = $this->createQueryBuilder('ppc');

		if ($criteria['product_id']) {
			$query->andWhere('ppc.product = :id')
				->setParameter('id', $criteria['product_id']);
		}

		if ($criteria['before']) {
			$query->andWhere('ppc.created_at <= :before')
				->setParameter('before', $criteria['before']);
		}

		if ($criteria['after']) {
			$query->andWhere('ppc.created_at >= :after')
				->setParameter('after', $criteria['after']);
		}

		return $query->getQuery()->getResult();
	}

}
