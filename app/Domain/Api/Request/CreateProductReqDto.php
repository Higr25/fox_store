<?php declare(strict_types = 1);

namespace App\Domain\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;


final class CreateProductReqDto
{

	#[Assert\NotBlank]
	#[Assert\Length(max: 50)]
	#[Assert\Type('string')]
	public string $name;

	#[Assert\NotBlank]
	#[Assert\PositiveOrZero]
	#[Assert\Type('float')]
	public float $price;

	#[Assert\PositiveOrZero]
	#[Assert\Type('integer')]
	public int $stock;

}
