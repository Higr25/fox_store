<?php declare(strict_types = 1);

namespace App\Domain\Api\Response;

use Apitte\Negotiation\Http\AbstractEntity;
use App\Model\Database\Entity\Product;

final class ProductResDto extends AbstractEntity
{

	public int $id;

	public string $name;

	public float $price;

	public int $stock;

	public \DateTimeInterface $created_at;

	public \DateTimeInterface $updated_at;

	public static function from(Product $product): self
	{
		$self = new self();
		$self->id = $product->getId();
		$self->name = $product->getName();
		$self->price = $product->getPrice();
		$self->stock = $product->getStock();
		$self->created_at = $product->getCreatedAt();
		$self->updated_at = $product->getUpdatedAt();

		return $self;
	}

}
