<?php

namespace Tests\Api;

use Tests\Support\ApiTester;

class OpenApiSchemaCest
{

	const URL = '/openapi/schema';

	public function _before(ApiTester $I): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
	}

	public function openapiSchema(ApiTester $I)
	{
		$I->sendGET(self::URL);
		$I->seeResponseCodeIs(200);
		$this->validateJsonSchema($I);
	}

	private function validateJsonSchema(ApiTester $I): void
	{
		$I->seeResponseContainsJson([
		'openapi' => '3.0.2',
		'info' => [
			'title' => 'OpenAPI',
			'version' => '1.0.0',
		],
		'paths' => [
			'/api/v1/openapi/schema' => [
				'get' => [
					'tags' => ['OpenApi'],
					'summary' => 'Get OpenAPI definition.',
					'responses' => [],
				],
			],
			'/api/v1/product/create' => [
				'post' => [
					'tags' => ['Products'],
					'summary' => 'Create new product in store. Maximum name length is 50 characters.',
					'requestBody' => [
						'content' => [
							'application/json' => [
								'schema' => [
									'type' => 'object',
									'properties' => [
										'name' => ['type' => 'string'],
										'price' => ['type' => 'number'],
										'stock' => ['type' => 'integer'],
									],
								],
							],
						],
					],
					'responses' => [
						'201' => ['description' => 'Product created'],
						'409' => ['description' => 'Product Name already exists'],
					],
				],
			],
			'/api/v1/products/price-history' => [
				'get' => [
					'tags' => ['Products'],
					'summary' => 'List products price history changes.',
					'parameters' => [
						[
							'name' => 'product_id',
							'in' => 'query',
							'description' => 'ID of the product to search for in history',
							'required' => false,
							'schema' => ['type' => 'integer'],
						],
						[
							'name' => 'before',
							'in' => 'query',
							'description' => 'String in DateTime format Y-m-d\TH:i:s to set as minimum date and time from which search history',
							'required' => false,
							'schema' => ['type' => 'string'],
						],
						[
							'name' => 'after',
							'in' => 'query',
							'description' => 'String in DateTime format Y-m-d\TH:i:s to set as maximum date and time to which search history',
							'required' => false,
							'schema' => ['type' => 'string'],
						],
					],
					'responses' => [
						'200' => [
							'description' => 'Found price changes in history',
							'content' => [
								'application/json' => [
									'schema' => [
										'type' => 'array',
										'items' => [
											'type' => 'object',
											'properties' => [
												'id' => ['type' => 'integer'],
												'product_id' => ['type' => 'integer'],
												'old_price' => ['type' => 'number'],
												'new_price' => ['type' => 'number'],
												'created_at' => ['type' => 'object'],
											],
										],
									],
								],
							],
						],
					],
				],
			],
			'/api/v1/products/{id}/delete' => [
				'delete' => [
					'tags' => ['Products'],
					'summary' => 'Remove product from store.',
					'parameters' => [
						[
							'name' => 'id',
							'in' => 'path',
							'description' => 'ID of product to delete.',
							'required' => true,
							'schema' => ['type' => 'integer'],
						],
					],
					'responses' => [
						'200' => ['description' => 'Product deleted'],
					],
				],
			],
			'/api/v1/products/{id}/edit' => [
				'patch' => [
					'tags' => ['Products'],
					'summary' => 'Update product in store.',
					'parameters' => [
						[
							'name' => 'id',
							'in' => 'path',
							'description' => 'ID of product to edit.',
							'required' => true,
							'schema' => ['type' => 'integer'],
						],
					],
					'requestBody' => [
						'content' => [
							'application/json' => [
								'schema' => [
									'type' => 'object',
									'properties' => [
										'name' => ['nullable' => true, 'type' => 'string'],
										'price' => ['nullable' => true, 'type' => 'number'],
										'stock' => ['nullable' => true, 'type' => 'integer'],
										'stockMod' => ['nullable' => true, 'type' => 'integer'],
									],
								],
							],
						],
					],
					'responses' => [
						'200' => [
							'description' => 'Updated Product',
							'content' => [
								'application/json' => [
									'schema' => [
										'type' => 'object',
										'properties' => [
											'id' => ['type' => 'integer'],
											'name' => ['type' => 'string'],
											'price' => ['type' => 'number'],
											'stock' => ['type' => 'integer'],
											'created_at' => ['type' => 'object'],
											'updated_at' => ['type' => 'object'],
										],
									],
								],
							],
						],
						'404' => ['description' => 'Product not found'],
					],
				],
			],
			'/api/v1/products' => [
				'get' => [
					'tags' => ['Products'],
					'summary' => 'List products in store.',
					'parameters' => [
						[
							'name' => 'name',
							'in' => 'query',
							'description' => 'Name of the product to search for',
							'required' => false,
							'schema' => ['type' => 'string'],
						],
						[
							'name' => 'stock_min',
							'in' => 'query',
							'description' => 'Minimum required stock amount',
							'required' => false,
							'schema' => ['type' => 'integer'],
						],
						[
							'name' => 'stock_max',
							'in' => 'query',
							'description' => 'Maximum required stock amount',
							'required' => false,
							'schema' => ['type' => 'integer'],
						],
					],
					'responses' => [
						'200' => [
							'description' => 'Found products',
							'content' => [
								'application/json' => [
									'schema' => [
										'type' => 'array',
										'items' => [
											'type' => 'object',
											'properties' => [
												'id' => ['type' => 'integer'],
												'name' => ['type' => 'string'],
												'price' => ['type' => 'number'],
												'stock' => ['type' => 'integer'],
												'created_at' => ['type' => 'object'],
												'updated_at' => ['type' => 'object'],
											],
										],
									],
								],
							],
						],
					],
				],
			],
		],
	]
);

	}
}
