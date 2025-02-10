<?php

namespace App\Domain\Api\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class ProductNameQuery
{

	/**
	 * @OA\Schema(
	 *     schema="ProductNameQuery",
	 *     type="string",
	 *     maxLength=50,
	 *     example="Jablko",
	 *     description="Name of the product to search for."
	 * )
	 */
	public string $name;
}

