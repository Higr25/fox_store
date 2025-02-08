<?php declare(strict_types = 1);

namespace App\Model\Database\Entity;

use App\Model\Database\Entity\Trait\TCreatedAt;
use App\Model\Database\Entity\Trait\TId;
use Doctrine\ORM\Mapping as ORM;
use App\Model\Database\Repository\ProductPriceChangeRepository;

/**
 * @ORM\Entity(repositoryClass="App\Model\Database\Repository\ProductPriceChangeRepository")
 * @ORM\Table(name="`product_price_change`")
 * @ORM\HasLifecycleCallbacks
 */
class ProductPriceChange extends AbstractEntity
{
	use TId;
	use TCreatedAt;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Model\Database\Entity\Product")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 */
	private Product $product;

	/** @ORM\Column(type="float", precision=6, scale=2, nullable=FALSE, unique=FALSE) */
	private float $old_price;

	/** @ORM\Column(type="float", precision=6, scale=2, nullable=FALSE, unique=FALSE) */
	private float $new_price;


	public function __construct(Product $product, float $old_price, float $new_price)
	{
		$this->product = $product;
		$this->old_price = $old_price;
		$this->new_price = $new_price;
	}

	public function getCreatedAt(): \DateTime
	{
		return $this->created_at;
	}

	public function getProduct(): Product
	{
		return $this->product;
	}

	public function setProduct(Product $product): void
	{
		$this->product = $product;
	}

	public function getOldPrice(): float
	{
		return $this->old_price;
	}

	public function setOldPrice(float $old_price): void
	{
		$this->old_price = $old_price;
	}

	public function getNewPrice(): float
	{
		return $this->new_price;
	}

	public function setNewPrice(float $new_price): void
	{
		$this->new_price = $new_price;
	}


}
