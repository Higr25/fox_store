<?php

namespace Tests\Api;

use Tests\Support\ApiTester;

class ProductListGETControllerCest
{

	const URL = '/products';

	public function getProducts(ApiTester $I)
	{
		$I->sendGET(self::URL);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		codecept_debug($I->grabResponse());
		$I->seeResponseContainsJson([
			['name' => 'Jablko', 'price' => 10.3, 'stock' => 3],
			['name' => 'Hřib', 'price' => 15.5, 'stock' => 5],
			['name' => 'Ořech', 'price' => 3.7, 'stock' => 7],
			['name' => 'Vlašský ořech', 'price' => 6.3, 'stock' => 10],
			['name' => 'Mrkev', 'price' => 8.9, 'stock' => 13],
			['name' => 'Malina', 'price' => 2.3, 'stock' => 15],
			['name' => 'Sklenice žluťoučkého medíku', 'price' => 42, 'stock' => 1]
		]);
	}


	public function getProductsByName(ApiTester $I)
	{
		$I->sendGET(self::URL, ['name' => 'Hřib']);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([['name' => 'Hřib', 'price' => 15.5, 'stock' => 5]]);
	}

	public function getProductsByStockRange(ApiTester $I)
	{
		$I->sendGET(self::URL, ['stock_min' => 5, 'stock_max' => 10]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			['name' => 'Hřib', 'price' => 15.5, 'stock' => 5],
			['name' => 'Ořech', 'price' => 3.7, 'stock' => 7],
			['name' => 'Vlašský ořech', 'price' => 6.3, 'stock' => 10],
		]);
	}

}
