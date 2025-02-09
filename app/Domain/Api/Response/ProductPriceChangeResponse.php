<?php declare(strict_types = 1);

namespace App\Domain\Api\Response;

use Apitte\Negotiation\Http\AbstractEntity;
use App\Model\Database\Entity\ProductPriceChange;

final class ProductPriceChangeResponse extends AbstractEntity
{

	public int $id;

	public int $product_id;

	public float $old_price;

	public float $new_price;

	public \DateTimeInterface $created_at;

	public static function from(ProductPriceChange $product): self
	{
		$self = new self();
		$self->id = $product->getId();
		$self->product_id = $product->getProduct()->getId();
		$self->old_price = $product->getOldPrice();
		$self->new_price = $product->getNewPrice();
		$self->created_at = $product->getCreatedAt();

		return $self;
	}

}
