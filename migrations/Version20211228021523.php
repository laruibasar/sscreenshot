<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211228021523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__screenshot AS SELECT id, url, width, height, output, file_type, lazy_load, dark_mode, grayscale, delay, user_agent, full_page, fail_on_error, clip_x, clip_y, clip_w, clip_h, created_on, filename, success FROM screenshot');
        $this->addSql('DROP TABLE screenshot');
        $this->addSql('CREATE TABLE screenshot (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL COLLATE BINARY, width SMALLINT DEFAULT NULL, height SMALLINT DEFAULT NULL, output VARCHAR(10) DEFAULT NULL, file_type VARCHAR(10) DEFAULT NULL, lazy_load BOOLEAN NOT NULL, dark_mode BOOLEAN NOT NULL, grayscale SMALLINT DEFAULT NULL, delay INTEGER DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, full_page BOOLEAN NOT NULL, fail_on_error BOOLEAN NOT NULL, clip_x SMALLINT DEFAULT NULL, clip_y SMALLINT DEFAULT NULL, clip_w SMALLINT DEFAULT NULL, clip_h SMALLINT DEFAULT NULL, created_on DATETIME NOT NULL, filename VARCHAR(255) DEFAULT NULL COLLATE BINARY, success BOOLEAN DEFAULT NULL)');
        $this->addSql('INSERT INTO screenshot (id, url, width, height, output, file_type, lazy_load, dark_mode, grayscale, delay, user_agent, full_page, fail_on_error, clip_x, clip_y, clip_w, clip_h, created_on, filename, success) SELECT id, url, width, height, output, file_type, lazy_load, dark_mode, grayscale, delay, user_agent, full_page, fail_on_error, clip_x, clip_y, clip_w, clip_h, created_on, filename, success FROM __temp__screenshot');
        $this->addSql('DROP TABLE __temp__screenshot');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__screenshot AS SELECT id, url, width, height, output, file_type, lazy_load, dark_mode, grayscale, delay, user_agent, full_page, fail_on_error, clip_x, clip_y, clip_w, clip_h, created_on, filename, success FROM screenshot');
        $this->addSql('DROP TABLE screenshot');
        $this->addSql('CREATE TABLE screenshot (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL, width SMALLINT NOT NULL, height SMALLINT NOT NULL, output VARCHAR(10) NOT NULL COLLATE BINARY, file_type VARCHAR(10) NOT NULL COLLATE BINARY, lazy_load BOOLEAN NOT NULL, dark_mode BOOLEAN NOT NULL, grayscale SMALLINT NOT NULL, delay INTEGER NOT NULL, user_agent VARCHAR(255) NOT NULL COLLATE BINARY, full_page BOOLEAN NOT NULL, fail_on_error BOOLEAN NOT NULL, clip_x SMALLINT DEFAULT NULL, clip_y SMALLINT DEFAULT NULL, clip_w SMALLINT DEFAULT NULL, clip_h SMALLINT DEFAULT NULL, created_on DATETIME NOT NULL, filename VARCHAR(255) DEFAULT NULL, success BOOLEAN DEFAULT NULL)');
        $this->addSql('INSERT INTO screenshot (id, url, width, height, output, file_type, lazy_load, dark_mode, grayscale, delay, user_agent, full_page, fail_on_error, clip_x, clip_y, clip_w, clip_h, created_on, filename, success) SELECT id, url, width, height, output, file_type, lazy_load, dark_mode, grayscale, delay, user_agent, full_page, fail_on_error, clip_x, clip_y, clip_w, clip_h, created_on, filename, success FROM __temp__screenshot');
        $this->addSql('DROP TABLE __temp__screenshot');
    }
}
