<?php declare(strict_types = 1);

namespace App\Model\Utils;

use Contributte\Utils\DateTime as ContributteDateTime;
use DateTimeInterface;

/**
 * @method DateTime modifyClone(string $modify = '')
 */
final class DateTime extends ContributteDateTime
{
	
	/**
	 * Create a DateTime object from a query parameter.
	 *
	 * @param string|null $dateString
	 * @return DateTimeInterface|null
	 */
	public static function createFromQueryParam(?string $dateString): ?DateTimeInterface
	{
		return $dateString
			? \DateTime::createFromFormat('Y-m-d\TH:i:s', $dateString) ?: null
			: null;
	}
	
}
