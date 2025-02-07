<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250702125744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates basic database data.';
    }

    public function up(Schema $schema): void
	{
		$this->addSql('
			INSERT INTO `product` (`name`, `price`, `stock`) VALUES
				("Jablko", 10.3, 3),
				("Hřib", 15.5, 5),
				("Ořech", 3.7, 7),
				("Vlašský ořech", 6.3, 10),
				("Mrkev", 8.9, 13),
				("Malina", 2.3, 15),
				("Sklenice žluťoučkého medíku", 42, 1);
		');
	}


    public function down(Schema $schema): void
    {
		$this->addSql('DELETE FROM `product`;');
    }
}
