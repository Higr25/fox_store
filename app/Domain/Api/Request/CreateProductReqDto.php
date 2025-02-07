<?php declare(strict_types = 1);

namespace App\Domain\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;


final class CreateProductReqDto
{

	#[Assert\NotBlank]
	#[Assert\Length(max: 50)]
	public string $name;

	#[Assert\NotBlank]
	#[Assert\PositiveOrZero]
	public float $price;

	#[Assert\NotBlank]
	#[Assert\PositiveOrZero]
	public int $stock;

}
