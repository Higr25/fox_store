<?php declare(strict_types = 1);

namespace App\Domain\Api\Facade;

use App\Domain\Api\Request\CreateProductReqDto;
use App\Domain\Api\Request\CreateUserReqDto;
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
//		var_dump($criteria);
//		die();
		$entities = $this->em->getRepository(Product::class)->findBy($criteria, $orderBy, $limit, $offset);
		$result = [];

		foreach ($entities as $entity) {
			$result[] = ProductResDto::from($entity);
		}

		return $result;
	}

	/**
	 * @return ProductResDto[]
	 */
	public function findAll(int $limit = 10, int $offset = 0): array
	{
		return $this->findBy([], ['id' => 'ASC'], $limit, $offset);
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

	public function findOne(int $id): ProductResDto
	{
		return $this->findOneBy(['id' => $id]);
	}

	public function create(CreateProductReqDto $dto): Product
	{
		$user = new Product(
			$dto->name,
			$dto->price,
			$dto->stock,
			$dto->created_at,
			$dto->updated_at,
		);

		$this->em->persist($user);
		$this->em->flush($user);

		return $user;
	}

}
