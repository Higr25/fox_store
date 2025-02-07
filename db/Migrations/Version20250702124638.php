<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250702124638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates basic database tables.';
    }

    public function up(Schema $schema): void
    {
       $this->createProductTable();
	   $this->createProductPriceHistoryTable();
    }

	private function createProductTable(): void
	{
		$this->addSql('
			DROP TABLE IF EXISTS `product`;
			CREATE TABLE `product` (
				`id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(50) NOT NULL,
				`price` FLOAT(6,2) NOT NULL,
				`stock` INT NOT NULL,
				`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
			) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
		');
		$this->addSql('CREATE INDEX `idx_product_name` ON `product` (`name`);');
		$this->addSql('CREATE INDEX `idx_product_stock` ON `product` (`stock`);');
	}

	private function createProductPriceHistoryTable(): void
	{
		$this->addSql('
			DROP TABLE IF EXISTS `product_price_change`;
			CREATE TABLE `product_price_change` (
				`id` INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
				`product_id` INT UNSIGNED NOT NULL,
				`old_price` FLOAT(6,2) NOT NULL,
				`new_price` FLOAT(6,2) NOT NULL,
				`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
			) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
		');
		$this->addSql('ALTER TABLE `product_price_change` ADD CONSTRAINT `fk_product_price_change_product_id` FOREIGN KEY (`product_id`) REFERENCES product (`id`);');
		$this->addSql('CREATE INDEX `idx_product_price_change_product_id` ON `product_price_change` (`product_id`);');
		$this->addSql('CREATE INDEX `idx_product_price_change_created_at` ON `product_price_change` (`created_at`);');
	}

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `product_price_change`;');
		$this->addSql('DROP TABLE `product`;');
    }
}
