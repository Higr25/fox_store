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

		$this->updateProduct($I);
		$I->sendGET(self::URL);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			[
			'product_id' => 1,
			'old_price' => 10.3,
			'new_price' => 11,
			]
		]);
	}

	private function updateProduct(ApiTester $I): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->sendPATCH('/products/1/edit', [
			'price' => 11.0,
			'stock' => 4
		]);
		$I->seeResponseCodeIs(200);
	}
}
