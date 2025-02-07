<?php

namespace Tests\Api;

use Tests\Support\ApiTester;

class ProductPOSTControllerCest
{

	const URL = '/product/create';

	public function createProductInvalidName(ApiTester $I)
	{
		// Product with invalid name (longer than 50 chars)
		$I->sendPOST(self::URL, [
			'name' => str_repeat('a', 51),
			'price' => 19.99,
			'stock' => 100
		]);
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
		$I->seeResponseContainsJson(['error' => 'Invalid name length.']);
	}

	public function createProductMissingParameters(ApiTester $I)
	{
		// Missing required fields
		$I->sendPOST(self::URL, [
			'name' => 'Test Product'
		]);
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
		$I->seeResponseContainsJson(['error' => 'Missing required parameter: price']);
	}
}
