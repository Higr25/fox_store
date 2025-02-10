<?php declare(strict_types = 1);

namespace App\Domain\Api\Facade;

use App\Domain\Api\Request\CreateProductRequest;
use App\Domain\Api\Request\UpdateProductRequest;
use App\Domain\Api\Response\ProductResponse;
use App\Model\Database\Entity\Product;
use App\Model\Database\EntityManagerDecorator;
use Apitte\Core\Exception\Api\ValidationException;

final class ProductsFacade
{

	public function __construct(private EntityManagerDecorator $em)
	{
	}

	/**
	 * @param mixed[] $criteria
	 * @param string[] $orderBy
	 * @return ProductResponse[]
	 */
	public function findBy(array $criteria = [], ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
	{
		$entities = $this->em->getRepository(Product::class)->findBy($criteria, $orderBy, $limit, $offset);
		$result = [];

		foreach ($entities as $entity) {
			$result[] = ProductResponse::from($entity);
		}

		return $result;
	}

	/**
	 * @param mixed[] $criteria
	 * @param string[] $orderBy
	 */
	public function findOneBy(array $criteria, ?array $orderBy = null): ?ProductResponse
	{
		$product = $this->em->getRepository(Product::class)->findOneBy($criteria, $orderBy);

		if ($product instanceof Product) {
			return ProductResponse::from($product);
		}

		return null;
	}

	public function delete(int $id): void
	{
		$product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);

		if ($product === null) {
			throw ValidationException::create()
				->withCode(404)
				->withMessage('Product not found');
		}

		$product->setActive(0);

		$this->em->persist($product);
		$this->em->flush($product);
	}

	public function update(int $id, UpdateProductRequest $dto): void
	{
		$product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);

		if ($product === null) {
			throw ValidationException::create()
				->withCode(404)
				->withMessage('Product not found');
		}

		if ($dto->name !== null) {
			$product->setName($dto->name);
		}
		if ($dto->price !== null) {
			$product->setPrice($dto->price);
		}
		if ($dto->stock !== null) {
			$product->setStock($dto->stock);
		}

		if ($dto->stockMod !== null) {
			$newValue = max(($product->getStock() + $dto->stockMod), 0);
			$product->setStock($newValue);
		}

		$this->em->persist($product);
		$this->em->flush($product);
	}

	public function create(CreateProductRequest $dto): void
	{
		$product = new Product(
			$dto->name,
			$dto->price,
			$dto->stock,
		);

		$this->em->persist($product);
		$this->em->flush($product);
	}

}
