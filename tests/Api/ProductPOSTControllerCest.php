<?php

namespace Tests\Api;

use Codeception\Example;
use Tests\Support\ApiTester;
use Codeception\Attribute\DataProvider;

class ProductPOSTControllerCest
{

	const URL = '/product/create';

	public function _before(ApiTester $I): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
	}

	#[DataProvider('productProvider')]
	public function createProducts(ApiTester $I, Example $example): void
	{
		$I->sendPOST(self::URL, [
			'name' => $example['name'],
			'price' => $example['price'],
			'stock' => $example['stock']
		]);

		if ($example['shouldFail']) {
			$I->seeResponseCodeIs(422);
			$I->seeResponseContains($example['message']);
			return;
		}

		$I->seeResponseCodeIs(201);

		$I->sendGET('/products?name='.$example['name']);
		$I->seeResponseContainsJson([
			'name' => $example['name'],
			'price' => $example['price'],
			'stock' => $example['stock']
		]);
	}

	public function createProductCustomParam(ApiTester $I): void
	{
		$I->sendPOST(self::URL, [
			'name' => 'Brambora',
			'price' => 10,
			'stock' => 10,
			'customParam' => 'customValue'
		]);

		$I->seeResponseCodeIs(422);
	}

	public function createProductUniqueName(ApiTester $I): void
	{
		$I->sendPOST(self::URL, [
			'name' => 'Brambora',
			'price' => 19.99,
			'stock' => 100
		]);
		$I->seeResponseCodeIs(201);

		$I->sendGET('/products?name=Brambora');
		$I->seeResponseContainsJson([
			'name' => 'Brambora',
			'price' => 19.99,
			'stock' => 100
		]);

		$I->sendPOST(self::URL, [
			'name' => 'Brambora',
			'price' => 19.99,
			'stock' => 100
		]);
		$I->seeResponseCodeIs(409);
	}

	protected function productProvider() : array
	{
		return [
			['name' => 'Brambora', 'price' => 10, 'stock' => 100, 'shouldFail' => false],
			['name' => 'Brambora', 'price' => -10, 'stock' => 100, 'shouldFail' => true, 'message' => 'This value should be either positive or zero'],
			['name' => 'Brambora', 'price' => '10', 'stock' => '100', 'shouldFail' => true, 'message' => 'Invalid data type in request body'],
			['name' => 'Brambora', 'price' => 'asdf', 'stock' => 'qwer', 'shouldFail' => true, 'message' => 'Invalid data type in request body'],
			['name' => 'Bramboraaaaaaaaaaaaaaadlouhabramboraaaaaaaaaaaaaaaaaaa', 'price' => 10, 'stock' => 10, 'shouldFail' => true, 'message' => 'This value is too long. It should have 50 characters or less.'],
		];
	}
}
