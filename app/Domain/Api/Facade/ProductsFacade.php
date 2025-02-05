<?php declare(strict_types = 1);

namespace App\Domain\Api\Facade;

use App\Domain\Api\Request\CreateProductReqDto;
use App\Domain\Api\Request\CreateUserReqDto;
use App\Domain\Api\Request\UpdateProductReqDto;
use App\Domain\Api\Response\ProductResDto;
use App\Domain\Api\Response\UserResDto;
use App\Domain\Product\Product;
use App\Domain\User\User;
use App\Model\Database\EntityManagerDecorator;
use App\Model\Exception\Runtime\Database\EntityNotFoundException;
use App\Model\Security\Passwords;
use Doctrine\Common\Collections\Criteria;

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
		$entity = $this->em->getRepository(Product::class)->findOneBy($criteria, $orderBy);

		if ($entity === null) {
			throw new EntityNotFoundException();
		}

		return ProductResDto::from($entity);
	}

	public function delete(int $id): void
	{
		$product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);

		$this->em->remove($product);
		$this->em->flush($product);
	}

	public function update(int $id, UpdateProductReqDto $dto): void
	{
		$product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);

		if ($dto->name) {
			$product->setName($dto->name);
		}
		if ($dto->price) {
			$product->setPrice($dto->price);
		}
		if ($dto->stock) {
			$product->setStock($dto->stock);
		}

		if ($dto->changeStock) {
			$product->setStock($product->getStock() + $dto->changeStock);
		}

		$this->em->persist($product);
		$this->em->flush($product);
	}

	public function create(CreateProductReqDto $dto): void
	{
		$product = new Product( // use request object here
			$dto->name,
			$dto->price,
			$dto->stock,
		);

		$this->em->persist($product);
		$this->em->flush($product);
	}

}
