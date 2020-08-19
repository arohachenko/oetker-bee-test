<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200817143711 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE record DROP FOREIGN KEY FK_9B349F91B7970CF8');
        $this->addSql('ALTER TABLE record ADD CONSTRAINT FK_9B349F91B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE record DROP FOREIGN KEY FK_9B349F91B7970CF8');
        $this->addSql('ALTER TABLE record ADD CONSTRAINT FK_9B349F91B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
    }
}
