<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200818045828 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE artist DROP INDEX name_idx, ADD UNIQUE INDEX name_uniq (name)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE artist DROP INDEX name_uniq, ADD INDEX name_idx (name)');
    }
}
