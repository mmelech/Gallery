<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220907213619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(45) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX uq_categories_title ON categories (title)');
        $this->addSql('CREATE TABLE comments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, post_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL, content CLOB NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_5F9E962A4B89032C ON comments (post_id)');
        $this->addSql('CREATE INDEX IDX_5F9E962AF675F31B ON comments (author_id)');
        $this->addSql('CREATE TABLE posts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , title VARCHAR(120) NOT NULL, content CLOB NOT NULL)');
        $this->addSql('CREATE INDEX IDX_885DBAFA12469DE2 ON posts (category_id)');
        $this->addSql('CREATE TABLE post_tag (posts_tags INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(posts_tags, tag_id))');
        $this->addSql('CREATE INDEX IDX_5ACE3AF0D5ECAD9F ON post_tag (posts_tags)');
        $this->addSql('CREATE INDEX IDX_5ACE3AF0BAD26311 ON post_tag (tag_id)');
        $this->addSql('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(45) NOT NULL)');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP INDEX email_idx');
        $this->addSql('DROP INDEX UNIQ_1483A5E96FF8BF36');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, user_data_id, email, roles, password FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_data_id INTEGER NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, CONSTRAINT FK_1483A5E96FF8BF36 FOREIGN KEY (user_data_id) REFERENCES users_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO users (id, user_data_id, email, roles, password) SELECT id, user_data_id, email, roles, password FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX email_idx ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E96FF8BF36 ON users (user_data_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gallery (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(45) NOT NULL COLLATE BINARY)');
        $this->addSql('CREATE TABLE photo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, gallery_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, title VARCHAR(45) NOT NULL COLLATE BINARY, description CLOB NOT NULL COLLATE BINARY)');
        $this->addSql('CREATE INDEX IDX_14B784184E7AF8F ON photo (gallery_id)');
        $this->addSql('CREATE INDEX IDX_14B78418A76ED395 ON photo (user_id)');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE post_tag');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP INDEX UNIQ_1483A5E96FF8BF36');
        $this->addSql('DROP INDEX email_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, user_data_id, email, roles, password FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_data_id INTEGER NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO users (id, user_data_id, email, roles, password) SELECT id, user_data_id, email, roles, password FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E96FF8BF36 ON users (user_data_id)');
        $this->addSql('CREATE UNIQUE INDEX email_idx ON users (email)');
    }
}
