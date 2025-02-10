<?php

namespace Tests\Api;

use Tests\Support\ApiTester;

class ProductDELETEControllerCest
{

	public function deleteProduct(ApiTester $I)
	{
		$I->sendGet('/products');
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson(['name' => 'Jablko']);

		$productId = $I->grabFromDatabase('product', 'id', ['name' => 'Jablko']);
		$I->sendDELETE("/products/$productId/delete");
		$I->seeResponseCodeIs(200);

		$I->sendGet('/products');
		$I->seeResponseCodeIs(200);
		$I->cantSeeResponseContainsJson(['name' => 'Jablko']);

		$I->seeInDatabase('product', ['id' => $productId, 'name' => 'Jablko', 'active' => 0]);
	}

}
