<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230603202655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_5F9E962AF675F31B');
        $this->addSql('DROP INDEX IDX_5F9E962A7E9E4C8C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comments AS SELECT id, photo_id, author_id, content, date FROM comments');
        $this->addSql('DROP TABLE comments');
        $this->addSql('CREATE TABLE comments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, photo_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL, content CLOB NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_5F9E962A7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5F9E962AF675F31B FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO comments (id, photo_id, author_id, content, date) SELECT id, photo_id, author_id, content, date FROM __temp__comments');
        $this->addSql('DROP TABLE __temp__comments');
        $this->addSql('CREATE INDEX IDX_5F9E962AF675F31B ON comments (author_id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A7E9E4C8C ON comments (photo_id)');
        $this->addSql('DROP INDEX IDX_876E0D94E7AF8F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__photos AS SELECT id, gallery_id, date, title, content FROM photos');
        $this->addSql('DROP TABLE photos');
        $this->addSql('CREATE TABLE photos (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, gallery_id INTEGER NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , title VARCHAR(120) NOT NULL, content CLOB NOT NULL, filename VARCHAR(191) DEFAULT NULL, CONSTRAINT FK_876E0D94E7AF8F FOREIGN KEY (gallery_id) REFERENCES galleries (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO photos (id, gallery_id, date, title, content) SELECT id, gallery_id, date, title, content FROM __temp__photos');
        $this->addSql('DROP TABLE __temp__photos');
        $this->addSql('CREATE INDEX IDX_876E0D94E7AF8F ON photos (gallery_id)');
        $this->addSql('DROP INDEX IDX_8C2D8E57BAD26311');
        $this->addSql('DROP INDEX IDX_8C2D8E57F8A626C7');
        $this->addSql('CREATE TEMPORARY TABLE __temp__photo_tag AS SELECT photos_tags, tag_id FROM photo_tag');
        $this->addSql('DROP TABLE photo_tag');
        $this->addSql('CREATE TABLE photo_tag (photos_tags INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(photos_tags, tag_id), CONSTRAINT FK_8C2D8E57F8A626C7 FOREIGN KEY (photos_tags) REFERENCES photos (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8C2D8E57BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO photo_tag (photos_tags, tag_id) SELECT photos_tags, tag_id FROM __temp__photo_tag');
        $this->addSql('DROP TABLE __temp__photo_tag');
        $this->addSql('CREATE INDEX IDX_8C2D8E57BAD26311 ON photo_tag (tag_id)');
        $this->addSql('CREATE INDEX IDX_8C2D8E57F8A626C7 ON photo_tag (photos_tags)');
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
        $this->addSql('DROP INDEX IDX_5F9E962A7E9E4C8C');
        $this->addSql('DROP INDEX IDX_5F9E962AF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comments AS SELECT id, photo_id, author_id, content, date FROM comments');
        $this->addSql('DROP TABLE comments');
        $this->addSql('CREATE TABLE comments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, photo_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL, content CLOB NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO comments (id, photo_id, author_id, content, date) SELECT id, photo_id, author_id, content, date FROM __temp__comments');
        $this->addSql('DROP TABLE __temp__comments');
        $this->addSql('CREATE INDEX IDX_5F9E962A7E9E4C8C ON comments (photo_id)');
        $this->addSql('CREATE INDEX IDX_5F9E962AF675F31B ON comments (author_id)');
        $this->addSql('DROP INDEX IDX_8C2D8E57F8A626C7');
        $this->addSql('DROP INDEX IDX_8C2D8E57BAD26311');
        $this->addSql('CREATE TEMPORARY TABLE __temp__photo_tag AS SELECT photos_tags, tag_id FROM photo_tag');
        $this->addSql('DROP TABLE photo_tag');
        $this->addSql('CREATE TABLE photo_tag (photos_tags INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(photos_tags, tag_id))');
        $this->addSql('INSERT INTO photo_tag (photos_tags, tag_id) SELECT photos_tags, tag_id FROM __temp__photo_tag');
        $this->addSql('DROP TABLE __temp__photo_tag');
        $this->addSql('CREATE INDEX IDX_8C2D8E57F8A626C7 ON photo_tag (photos_tags)');
        $this->addSql('CREATE INDEX IDX_8C2D8E57BAD26311 ON photo_tag (tag_id)');
        $this->addSql('DROP INDEX IDX_876E0D94E7AF8F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__photos AS SELECT id, gallery_id, date, title, content FROM photos');
        $this->addSql('DROP TABLE photos');
        $this->addSql('CREATE TABLE photos (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, gallery_id INTEGER NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , title VARCHAR(120) NOT NULL, content CLOB NOT NULL)');
        $this->addSql('INSERT INTO photos (id, gallery_id, date, title, content) SELECT id, gallery_id, date, title, content FROM __temp__photos');
        $this->addSql('DROP TABLE __temp__photos');
        $this->addSql('CREATE INDEX IDX_876E0D94E7AF8F ON photos (gallery_id)');
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
