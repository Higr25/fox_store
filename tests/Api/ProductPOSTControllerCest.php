<?php

namespace Tests\Api;

use Tests\Support\ApiTester;

class ProductPOSTControllerCest
{

	const URL = '/product/create';

	public function _before(ApiTester $I): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
	}

	public function createProduct(ApiTester $I)
	{
		$I->sendPOST(self::URL, [
			'name' => 'Brambora',
			'price' => 19.99,
			'stock' => 100
		]);
		$I->seeResponseCodeIs(201);
	}

}
