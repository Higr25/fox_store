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
			'name' => 'Červené Jablko',
			'price' => 18.0,
			'stock' => 10
		]);

		$I->sendGet('/products?name=cervene_jablko');
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			'name' => 'Červené Jablko',
			'price' => 18.0,
			'stock' => 10
		]);
	}

	public function testStockMod(ApiTester $I) {
		$I->sendPATCH('/products/1/edit', [
			'stock_mod' => -3
		]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			'name' => 'Jablko',
			'stock' => 0
		]);

		$I->sendPATCH('/products/1/edit', [
			'stock_mod' => 30
		]);
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson([
			'name' => 'Jablko',
			'stock' => 30
		]);
	}

	public function testStockParams(ApiTester $I) {
		$I->sendPATCH('/products/1/edit', [
			'stock_mod' => -3,
			'stock' => 5,
		]);
		$I->seeResponseCodeIs(422);
		$I->seeResponseContains("Only one of 'stock' or 'stock_mod' must be set");
	}

	public function editNonExistingProduct(ApiTester $I)
	{
		$I->sendPATCH('/products/100/edit', [
			'price' => 11.0,
			'stock' => 4
		]);
		$I->seeResponseCodeIs(404);
	}

	public function editProductInvalidBody(ApiTester $I)
	{
		$I->sendPATCH('/products/100/edit', [
			'price' => 'asdf',
			'stock' => 4
		]);
		$I->seeResponseCodeIs(422);
		$I->seeResponseContains('Invalid data type in request body');
	}

}
