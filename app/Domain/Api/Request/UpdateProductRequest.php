<?php declare(strict_types = 1);

namespace App\Domain\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UpdateProductRequest
{

	#[Assert\Length(max: 50)]
	public ?string $name = null;

	#[Assert\PositiveOrZero]
	public ?float $price = null;

	#[Assert\AtLeastOneOf([
		new Assert\NotBlank(),
		new Assert\IsNull(),
	])]
	public ?int $stock = null;

	#[Assert\AtLeastOneOf([
		new Assert\NotBlank(),
		new Assert\IsNull(),
	])]
	public ?int $stockMod = null;

	#[Assert\Callback]
	public function validateProducts(ExecutionContextInterface $context): void
	{
		if ($this->stock !== null && $this->stockMod !== null) {
			$context->buildViolation("Only one of 'stock' or 'stock_mod' must be set.")
				->atPath('stock')
				->addViolation();

			$context->buildViolation("Only one of 'stock' or 'stock_mod' must be set.")
				->atPath('stock_mod')
				->addViolation();
		}
	}
}
