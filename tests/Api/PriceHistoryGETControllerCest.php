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
		$I->seeResponseContainsJson($this->getBaseHistoryData());

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

	public function getHistoryByDate(ApiTester $I): void
	{
		$I->sendGET(self::URL.'?after=2025-01-02T00:00:00');
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			[
				'product_id' => 1,
				'old_price'  => 7.3,
				'new_price'  => 10.3,
				'created_at' => '2025-01-02T13:00:00+01:00'
			],
			[
				'product_id' => 2,
				'old_price'  => 9.6,
				'new_price'  => 12.7,
				'created_at' => '2025-01-03T14:00:00+01:00'
			],
			[
				'product_id' => 2,
				'old_price'  => 12.7,
				'new_price'  => 15.5,
				'created_at' => '2025-01-04T15:00:00+01:00'
			]
		]);

		$I->sendGET(self::URL.'?after=2025-01-02T00:00:00&before=2025-01-04T00:00:00');
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			[
				'product_id' => 1,
				'old_price'  => 7.3,
				'new_price'  => 10.3,
				'created_at' => '2025-01-02T13:00:00+01:00'
			],
			[
				'product_id' => 2,
				'old_price'  => 9.6,
				'new_price'  => 12.7,
				'created_at' => '2025-01-03T14:00:00+01:00'
			]
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

	private function getBaseHistoryData(): array
	{
		return [
			[
				'product_id' => 1,
				'old_price'  => 5.2,
				'new_price'  => 7.3,
				'created_at' => '2025-01-01T12:00:00+01:00'
			],
			[
				'product_id' => 1,
				'old_price'  => 7.3,
				'new_price'  => 10.3,
				'created_at' => '2025-01-02T13:00:00+01:00'
			],
			[
				'product_id' => 2,
				'old_price'  => 9.6,
				'new_price'  => 12.7,
				'created_at' => '2025-01-03T14:00:00+01:00'
			],
			[
				'product_id' => 2,
				'old_price'  => 12.7,
				'new_price'  => 15.5,
				'created_at' => '2025-01-04T15:00:00+01:00'
			]
		];
	}
}
