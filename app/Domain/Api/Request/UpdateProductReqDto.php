<?php declare(strict_types = 1);

namespace App\Domain\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class UpdateProductReqDto
{
	/**
	 * @Assert\Length(max=50)
	 * @Apitte\OpenApi("New name of the product.")
	 */
	public ?string $name = null;
	
	/**
	 * @Assert\Positive
	 * @Apitte\OpenApi("New price of the product.")
	 */
	public ?float $price = null;
	
	/**
	 * @Apitte\OpenApi("New stock amount of the product.")
	 */
	public ?int $stock = null;
	
	/**
	 * @Apitte\OpenApi("Stock amount difference of the product. Accepts negative values.")
	 */
	public ?int $changeStock = null;
}
