<?php

namespace Tests\Api;

use Tests\Support\ApiTester;

class ProductPATCHControllerCest
{

	public function _before(ApiTester $I): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
	}

	public function editProduct(ApiTester $I)
	{
		$I->sendPATCH('/products/1/edit', [
			'price' => 11.0,
			'stock' => 4
		]);
		$I->seeResponseCodeIs(200);
	}

}
