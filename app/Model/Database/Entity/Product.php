<?php declare(strict_types = 1);

namespace App\Model\Database\Entity;

use App\Model\Database\Entity\AbstractEntity;
use App\Model\Database\Entity\Trait\TCreatedAt;
use App\Model\Database\Entity\Trait\TId;
use App\Model\Database\Entity\Trait\TUpdatedAt;
use Doctrine\ORM\Mapping as ORM;
use App\Model\Database\Repository\ProductRepository;

/**
 * @ORM\Entity(repositoryClass="App\Model\Database\Repository\ProductRepository")
 * @ORM\Table(name="`product`")
 * @ORM\HasLifecycleCallbacks
 */
class Product extends AbstractEntity
{

	use TId;
	use TCreatedAt;
	use TUpdatedAt;


	/** @ORM\Column(type="string", length=50, nullable=FALSE, unique=TRUE) */
	private string $name;

	/** @ORM\Column(type="float", precision=6, scale=2, nullable=FALSE, unique=FALSE) */
	private float $price;

	/** @ORM\Column(type="integer", length=10, nullable=FALSE, unique=FALSE) */
	private int $stock;

	public function __construct(string $name, float $price, int $stock)
	{
		$this->name = $name;
		$this->price = $price;
		$this->stock = $stock;
	}

	public function getCreatedAt(): \DateTime
	{
		return $this->created_at;
	}

	public function getUpdatedAt(): \DateTime
	{
		return $this->updated_at;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getPrice(): float
	{
		return $this->price;
	}

	public function getStock(): int
	{
		return $this->stock;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function setPrice(float $price): void
	{
		$this->price = $price;
	}

	public function setStock(int $stock): void
	{
		$this->stock = $stock;
	}


}
