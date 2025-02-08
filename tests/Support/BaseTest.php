<?php

namespace Tests\Support;

use Codeception\Lib\Driver\Db;
use Codeception\Module;
use Codeception\TestInterface;

class BaseTest extends Module
{

	const FIXTURES_DIR = 'tests/Fixtures/';

	public Db $db;

	public function _beforeSuite($settings = []): void
	{
		$this->db = new Db("mysql:host=host.docker.internal;dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASS']);
	}

	public function _before(TestInterface $test): void
	{
		$testName = basename($test->getFileName(), '.php');

		try {
			$sql = file_get_contents(self::FIXTURES_DIR.$testName.'/'.$testName.'Fixture.sql');
			$this->db->load([$sql]);
		} catch (\Exception $e) {
			// continue
		}
	}

	public function _after(TestInterface $test): void
	{
		$testName = basename($test->getFileName(), '.php');

		try {
			$sql = file_get_contents(self::FIXTURES_DIR.$testName.'/'.$testName.'Cleanup.sql');
			$this->db->load([$sql]);
		} catch (\Exception $e) {
			// continue
		}
	}
}
