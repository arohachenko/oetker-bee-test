<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200819105348 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
CREATE TABLE users
  (
     id INT AUTO_INCREMENT NOT NULL,
     username VARCHAR(25) NOT NULL,
     UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username),
     PRIMARY KEY(id)
  )
DEFAULT CHARACTER SET utf8mb4
COLLATE `utf8mb4_unicode_ci`
ENGINE = InnoDB
SQL
);
        $this->addSql('INSERT INTO users (`username`) VALUES (\'admin\'), (\'user\');'
);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}
