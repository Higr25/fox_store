<?php

namespace App\Domain\Api\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class DateTimeStringQuery
{

	/** @OA\Schema(
	*     schema="DateTimeStringQuery",
	*     type="string",
	*     format="date-time",
	*     pattern="/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])T([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/",
	*     example="2025-02-05T12:34:56",
	*     description="String in DateTime format Y-m-d\TH:i:s"
	* )
	*/
	public $datetime;
}

