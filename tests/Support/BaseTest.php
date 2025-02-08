<?php

namespace Support;

use Codeception\Lib\Driver\Db;
use Codeception\Module;
use Codeception\TestInterface;
use Nette\Neon\Neon;


class BaseTest extends Module
{

	public Db $db;
	public array $config = [];
	public string $testName;


	public function _beforeSuite($settings = []) {
		$neonString = file_get_contents('config/local.neon');
		$neonArray = Neon::decode($neonString);
		var_dump($neonArray);
		$this->getModule(self::class)->_setConfig($neonArray['parameters']['database']);
		$dbHost = $this->_getConfig('host');
		$dbName = $this->_getConfig('dbname');
		$dbUser = $this->_getConfig('user');
		$dbPass = $this->_getConfig('password');

		codecept_debug($dbUser);
		$this->db = new Db("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
	}

	public function _before(TestInterface $test)
	{
		$testName = preg_match('/[^\/]+(?=\.php$)/', $test->getFileName(), $matches) ? $matches[0] : '';
		$this->testName = $testName;

		$sql = file_get_contents(codecept_root_dir().'tests/fixtures/'.$testName.'/'.$testName.'Fixture.sql');
		$this->db->load([$sql]);

		$this->deleteDirectory($this->_getConfig('cacheDir'));
		$this->login();
	}

	public function _after(TestInterface $test)
	{
		$testName = preg_match('/[^\/]+(?=\.php$)/', $test->getFileName(), $matches) ? $matches[0] : '';
		$sql = file_get_contents(codecept_root_dir().'tests/fixtures/'.$testName.'/'.$testName.'Cleanup.sql');
		$this->db->load([$sql]);

		$this->deleteDirectory($this->_getConfig('uploadDir'));
		$this->deleteDirectory($this->_getConfig('cacheDir'));
	}

	public function haveFileFixture(string $fileName): void
	{
		$sql = file_get_contents(codecept_root_dir().'tests/fixtures/'.$this->testName.'/'.$fileName.'.sql');
		$this->db->load([$sql]);
	}

	private function login()
	{
		$login = $this->_getConfig('testerUser');
		$password = $this->_getConfig('testerPassword');

		$I = $this->getModule('WebDriver');
		$I->amOnPage('/login');
		$I->fillField('username', $login);
		$I->fillField('password', $password);
		$I->click('button[type=submit]');
		$I->waitForElement('.t-logout');
	}

	private function deleteDirectory($dir)
	{
		if (!file_exists($dir)) {
			return true;
		}

		if (!is_dir($dir)) {
			return unlink($dir);
		}

		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}

			if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
				return false;
			}
		}

		return rmdir($dir);
	}
}
