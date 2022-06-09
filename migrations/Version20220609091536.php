<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220609091536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE playlist (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D782112DA76ED395 ON playlist (user_id)');
        $this->addSql('CREATE TABLE playlist_song (playlist_id INTEGER NOT NULL, song_id INTEGER NOT NULL, PRIMARY KEY(playlist_id, song_id))');
        $this->addSql('CREATE INDEX IDX_93F4D9C36BBD148 ON playlist_song (playlist_id)');
        $this->addSql('CREATE INDEX IDX_93F4D9C3A0BDB2F3 ON playlist_song (song_id)');
        $this->addSql('DROP INDEX UNIQ_1599687A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__artist AS SELECT id, user_id, name FROM artist');
        $this->addSql('DROP TABLE artist');
        $this->addSql('CREATE TABLE artist (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_1599687A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO artist (id, user_id, name) SELECT id, user_id, name FROM __temp__artist');
        $this->addSql('DROP TABLE __temp__artist');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1599687A76ED395 ON artist (user_id)');
        $this->addSql('DROP INDEX IDX_722870DB7970CF8');
        $this->addSql('DROP INDEX IDX_722870DA0BDB2F3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__song_artist AS SELECT song_id, artist_id FROM song_artist');
        $this->addSql('DROP TABLE song_artist');
        $this->addSql('CREATE TABLE song_artist (song_id INTEGER NOT NULL, artist_id INTEGER NOT NULL, PRIMARY KEY(song_id, artist_id), CONSTRAINT FK_722870DA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_722870DB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO song_artist (song_id, artist_id) SELECT song_id, artist_id FROM __temp__song_artist');
        $this->addSql('DROP TABLE __temp__song_artist');
        $this->addSql('CREATE INDEX IDX_722870DB7970CF8 ON song_artist (artist_id)');
        $this->addSql('CREATE INDEX IDX_722870DA0BDB2F3 ON song_artist (song_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE playlist');
        $this->addSql('DROP TABLE playlist_song');
        $this->addSql('DROP INDEX UNIQ_1599687A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__artist AS SELECT id, user_id, name FROM artist');
        $this->addSql('DROP TABLE artist');
        $this->addSql('CREATE TABLE artist (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO artist (id, user_id, name) SELECT id, user_id, name FROM __temp__artist');
        $this->addSql('DROP TABLE __temp__artist');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1599687A76ED395 ON artist (user_id)');
        $this->addSql('DROP INDEX IDX_722870DA0BDB2F3');
        $this->addSql('DROP INDEX IDX_722870DB7970CF8');
        $this->addSql('CREATE TEMPORARY TABLE __temp__song_artist AS SELECT song_id, artist_id FROM song_artist');
        $this->addSql('DROP TABLE song_artist');
        $this->addSql('CREATE TABLE song_artist (song_id INTEGER NOT NULL, artist_id INTEGER NOT NULL, PRIMARY KEY(song_id, artist_id))');
        $this->addSql('INSERT INTO song_artist (song_id, artist_id) SELECT song_id, artist_id FROM __temp__song_artist');
        $this->addSql('DROP TABLE __temp__song_artist');
        $this->addSql('CREATE INDEX IDX_722870DA0BDB2F3 ON song_artist (song_id)');
        $this->addSql('CREATE INDEX IDX_722870DB7970CF8 ON song_artist (artist_id)');
    }
}
