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

	private function loadConfigFromAnnotation(TestInterface $test)
	{
		$annotations = Annotations::forTest($test);

		if (isset($annotations['config'])) {
			$configFile = $annotations['config'][0]; // Assuming the annotation holds the file name
			$configPath = __DIR__ . "/../../config/env/{$configFile}.neon";

			if (file_exists($configPath)) {
				$neonData = file_get_contents($configPath);
				$config = Neon::decode($neonData);
				// You can now inject this config into your application or container
				// Example: $this->getModule('NetteTester')->setConfig($config);
			} else {
				throw new \Exception("Config file {$configFile}.neon not found.");
			}
		}
	}
}
