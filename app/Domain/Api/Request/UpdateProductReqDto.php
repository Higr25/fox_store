<?php declare(strict_types = 1);

namespace App\Domain\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Apitte\OpenApi;

final class UpdateProductReqDto
{
	#[Assert\Length(max: 50)]
	public ?string $name = null;

	#[Assert\PositiveOrZero]
	public ?float $price = null;

	#[Assert\PositiveOrZero]
	public ?int $stock = null;

	#[Assert\NotEqualTo(0)]
	public ?int $changeStock = null;
}
