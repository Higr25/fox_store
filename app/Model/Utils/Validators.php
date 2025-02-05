<?php declare(strict_types = 1);

namespace App\Model\Utils;

use Apitte\Core\Exception\Api\ValidationException;
use Contributte\Utils\Validators as ContributteValidators;
use Nette\Http\IResponse;

final class Validators extends ContributteValidators
{

	public static function integer(mixed $value): void
	{
		if (!Caster::toInt($value) || $value < 0) {
			throw ValidationException::create()
				->withCode(IResponse::S400_BadRequest)
				->withMessage("$value' is not a valid value");
		}
	}
	public static function dateTime(string $dateTimeString): void
	{
		$dateTime = DateTime::createFromQueryParam($dateTimeString);
		if (!$dateTime) {
			throw ValidationException::create()
				->withCode(IResponse::S400_BadRequest)
				->withMessage("$dateTimeString is not in valid format ".DateTime::FORMAT." (valid example: 2025-02-05T12:34:56)");
		}
	}
}
