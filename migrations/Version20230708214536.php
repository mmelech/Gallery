<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230708214536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, photo_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL, content CLOB NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_5F9E962A7E9E4C8C ON comments (photo_id)');
        $this->addSql('CREATE INDEX IDX_5F9E962AF675F31B ON comments (author_id)');
        $this->addSql('CREATE TABLE galleries (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(45) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX uq_galleries_title ON galleries (title)');
        $this->addSql('CREATE TABLE photos (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, gallery_id INTEGER DEFAULT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , title VARCHAR(120) NOT NULL, content CLOB NOT NULL, filename VARCHAR(191) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_876E0D94E7AF8F ON photos (gallery_id)');
        $this->addSql('CREATE TABLE photo_tag (photos_tags INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(photos_tags, tag_id))');
        $this->addSql('CREATE INDEX IDX_8C2D8E57F8A626C7 ON photo_tag (photos_tags)');
        $this->addSql('CREATE INDEX IDX_8C2D8E57BAD26311 ON photo_tag (tag_id)');
        $this->addSql('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(45) NOT NULL)');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_data_id INTEGER NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E96FF8BF36 ON users (user_data_id)');
        $this->addSql('CREATE UNIQUE INDEX email_idx ON users (email)');
        $this->addSql('CREATE TABLE users_data (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(128) DEFAULT NULL, firstname VARCHAR(128) DEFAULT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE galleries');
        $this->addSql('DROP TABLE photos');
        $this->addSql('DROP TABLE photo_tag');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_data');
    }
}
