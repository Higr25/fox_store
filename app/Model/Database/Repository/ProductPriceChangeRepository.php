<?php declare(strict_types = 1);

namespace App\Model\Database\Repository;

use App\Model\Database\Entity\ProductPriceChange;

/** @extends AbstractRepository<ProductPriceChange> */
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

		if ($orderBy) {
			foreach ($orderBy as $field => $direction) {
				$query->addOrderBy('ppc.' . $field, $direction);
			}
		}

		return $query->getQuery()->getResult();
	}

}
