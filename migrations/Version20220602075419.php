<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220602075419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE song (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, duration INTEGER NOT NULL, listeningnumber INTEGER NOT NULL, image VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE song_artist (song_id INTEGER NOT NULL, artist_id INTEGER NOT NULL, PRIMARY KEY(song_id, artist_id))');
        $this->addSql('CREATE INDEX IDX_722870DA0BDB2F3 ON song_artist (song_id)');
        $this->addSql('CREATE INDEX IDX_722870DB7970CF8 ON song_artist (artist_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('DROP INDEX UNIQ_1599687A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__artist AS SELECT id, user_id, name FROM artist');
        $this->addSql('DROP TABLE artist');
        $this->addSql('CREATE TABLE artist (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_1599687A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO artist (id, user_id, name) SELECT id, user_id, name FROM __temp__artist');
        $this->addSql('DROP TABLE __temp__artist');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1599687A76ED395 ON artist (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE song');
        $this->addSql('DROP TABLE song_artist');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP INDEX UNIQ_1599687A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__artist AS SELECT id, user_id, name FROM artist');
        $this->addSql('DROP TABLE artist');
        $this->addSql('CREATE TABLE artist (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO artist (id, user_id, name) SELECT id, user_id, name FROM __temp__artist');
        $this->addSql('DROP TABLE __temp__artist');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1599687A76ED395 ON artist (user_id)');
    }
}
