<?php declare(strict_types = 1);

namespace App\Model\Utils;

use Contributte\Utils\Strings as ContributteStrings;
use App\Model\Exception\Logic\InvalidArgumentException;

final class Strings extends ContributteStrings
{

	public static function dateTimeError(string $field, string $value): string
	{
		return 'Invalid DateTime query parameter ' . "'".$field."': ". "'".$value."'" . '. (valid example: 2025-02-05T12:34:56)';
	}

	public static function stringError(string $field, string $value): string
	{
		return 'Invalid string query parameter ' . "'".$field."': ". "'".$value."'" . '. (valid examples: orech, vlašský ořech, vlassky_orech)';
	}
}
