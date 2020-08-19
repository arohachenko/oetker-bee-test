<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200817075631 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<'SQL'
CREATE TABLE artist
  (
     id   INT UNSIGNED auto_increment NOT NULL,
     name VARCHAR(255) NOT NULL,
     INDEX name_idx (name),
     PRIMARY KEY(id)
  )
DEFAULT CHARACTER SET utf8mb4
COLLATE `utf8mb4_unicode_ci`
engine = InnoDB  
SQL
);
        $this->addSql(<<<'SQL'
CREATE TABLE record
  (
     id        INT UNSIGNED auto_increment NOT NULL,
     artist_id INT UNSIGNED DEFAULT NULL,
     title     VARCHAR(255) NOT NULL,
     label     VARCHAR(255) NOT NULL,
     year      SMALLINT UNSIGNED NOT NULL,
     type      VARCHAR(50) NOT NULL,
     INDEX IDX_9B349F91B7970CF8 (artist_id),
     INDEX title_idx (title),
     INDEX year_idx (year),
     PRIMARY KEY(id)
  )
DEFAULT CHARACTER SET utf8mb4
COLLATE `utf8mb4_unicode_ci`
engine = InnoDB  
SQL
);
        $this->addSql(<<<'SQL'
ALTER TABLE record
  ADD CONSTRAINT FK_9B349F91B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)  
SQL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE record DROP FOREIGN KEY FK_9B349F91B7970CF8');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE record');
    }
}
