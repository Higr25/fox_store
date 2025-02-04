<?php declare(strict_types = 1);

namespace App\Domain\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateProductReqDto
{

	/** @Assert\NotBlank */
	public string $name;

	/** @Assert\NotBlank */
	public float $price;

	/** @Assert\NotBlank */
	public int $stock;

	/** @Assert\NotBlank */
	public \DateTime $created_at;

	/** @Assert\NotBlank */
	public \DateTime $updated_at;

}
