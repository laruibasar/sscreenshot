<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211224192323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE screenshot (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL, width SMALLINT NOT NULL, height SMALLINT NOT NULL, output VARCHAR(10) NOT NULL, file_type VARCHAR(10) NOT NULL, lazy_load BOOLEAN NOT NULL, dark_mode BOOLEAN NOT NULL, grayscale SMALLINT NOT NULL, delay INTEGER NOT NULL, user_agent VARCHAR(255) NOT NULL, full_page BOOLEAN NOT NULL, fail_on_error BOOLEAN NOT NULL, clip_x SMALLINT DEFAULT NULL, clip_y SMALLINT DEFAULT NULL, clip_w SMALLINT DEFAULT NULL, clip_h SMALLINT DEFAULT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE screenshot');
    }
}
