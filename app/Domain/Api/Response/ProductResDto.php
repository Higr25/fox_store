<?php declare(strict_types = 1);

namespace App\Domain\Api\Response;

use App\Domain\Product\Product;
use DateTimeInterface;

final class ProductResDto
{

	public int $id;

	public string $name;

	public float $price;

	public int $stock;

	public \DateTimeInterface $created_at;

	public \DateTimeInterface $updated_at;

	public static function from(Product $user): self
	{
		$self = new self();
		$self->id = $user->getId();
		$self->name = $user->getName();
		$self->price = $user->getPrice();
		$self->stock = $user->getStock();
		$self->created_at = $user->getCreatedAt();
		$self->updated_at = $user->getUpdatedAt();

		return $self;
	}

}
