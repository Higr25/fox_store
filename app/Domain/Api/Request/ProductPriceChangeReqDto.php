<?php declare(strict_types = 1);

namespace App\Domain\Api\Request;

use App\Domain\Product\Product;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductPriceChangeReqDto
{
	/**
	 * @Assert\Positive
	 * @Apitte\OpenApi("Changed product.")
	 */
	public ?int $product_id = null;
	
	/**
	 * @Assert\Positive
	 * @Apitte\OpenApi("Old price of the product.")
	 */
	public ?float $old_price = null;
	
	/**
	 * @Assert\Positive
	 * @Apitte\OpenApi("New price of the product.")
	 */
	public ?float $new_price = null;
}
