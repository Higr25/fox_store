<?php

namespace Tests\Api;

use Tests\Support\ApiTester;

class ApiCest
{
	public function getProductById(ApiTester $I)
	{
		$I->sendGET('/products');
		$I->seeResponseCodeIs(200);
	}
}

