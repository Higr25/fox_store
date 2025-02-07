<?php declare(strict_types = 1);

namespace App\Domain\Api\Facade;

use App\Domain\Api\Request\CreateProductReqDto;
use App\Domain\Api\Request\UpdateProductReqDto;
use App\Domain\Api\Response\ProductResDto;
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
	 * @return ProductResDto[]
	 */
	public function findBy(array $criteria = [], ?array $orderBy = null, $limit = null, $offset = null): array
	{
		$entities = $this->em->getRepository(Product::class)->findBy($criteria, $orderBy, $limit, $offset);
		$result = [];

		foreach ($entities as $entity) {
			$result[] = ProductResDto::from($entity);
		}

		return $result;
	}

	/**
	 * @param mixed[] $criteria
	 * @param string[] $orderBy
	 */
	public function findOneBy(array $criteria, ?array $orderBy = null): ProductResDto
	{
		$product = $this->em->getRepository(Product::class)->findOneBy($criteria, $orderBy);

		if ($product === null) {
			throw ValidationException::create()
				->withCode(404)
				->withMessage('Product not found');
		}

		return ProductResDto::from($product);
	}

	public function delete(int $id): void
	{
		$product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);

		if ($product === null) {
			throw ValidationException::create()
				->withCode(404)
				->withMessage('Product not found');
		}

		$this->em->remove($product);
		$this->em->flush($product);
	}

	public function update(int $id, UpdateProductReqDto $dto): void
	{
		$product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);

		if ($product === null) {
			throw ValidationException::create()
				->withCode(404)
				->withMessage('Product not found');
		}

		if ($dto->name) {
			$product->setName($dto->name);
		}
		if ($dto->price) {
			$product->setPrice($dto->price);
		}
		if ($dto->stock) {
			$product->setStock($dto->stock);
		}

		if ($dto->stockMod) {
			$product->setStock($product->getStock() + $dto->stockMod);
		}

		$this->em->persist($product);
		$this->em->flush($product);
	}

	public function create(CreateProductReqDto $dto): void
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
