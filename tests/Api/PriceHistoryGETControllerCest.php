<?php

namespace Tests\Api;

use Tests\Support\ApiTester;

class PriceHistoryGETControllerCest
{

	const URL = '/products/price-history';

	public function _before(ApiTester $I): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
	}

	public function getHistory(ApiTester $I)
	{
		$I->sendGET(self::URL);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([]);

		$this->updateProduct($I, 10.53);
		$I->sendGET(self::URL);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			[
			'product_id' => 1,
			'old_price' => 10.3,
			'new_price' => 10.53,
			]
		]);

		$this->updateProduct($I, 13.49);
		$I->sendGET(self::URL);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			[
				'product_id' => 1,
				'old_price' => 10.3,
				'new_price' => 10.53,
			],
			[
				'product_id' => 1,
				'old_price' => 10.53,
				'new_price' => 13.49,
			],

		]);
	}

	private function updateProduct(ApiTester $I, float $newPrice): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->sendPATCH('/products/1/edit', [
			'price' => $newPrice,
			'stock' => 4
		]);
		$I->seeResponseCodeIs(200);
	}
}
