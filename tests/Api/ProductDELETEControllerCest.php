<?php

namespace Tests\Api;

use Tests\Support\ApiTester;

class ProductDELETEControllerCest
{

	public function deleteProduct(ApiTester $I)
	{
		$I->sendDELETE('/products/1/delete');
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson(['message' => 'Product deleted.']);
	}

}
