<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230603202558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE comments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, photo_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL, content CLOB NOT NULL COLLATE BINARY, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_5F9E962AF675F31B ON comments (author_id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A7E9E4C8C ON comments (photo_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE galleries (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(45) NOT NULL COLLATE BINARY)');
        $this->addSql('CREATE UNIQUE INDEX uq_galleries_title ON galleries (title)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE photo_tag (photos_tags INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(photos_tags, tag_id))');
        $this->addSql('CREATE INDEX IDX_8C2D8E57BAD26311 ON photo_tag (tag_id)');
        $this->addSql('CREATE INDEX IDX_8C2D8E57F8A626C7 ON photo_tag (photos_tags)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE photos (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, gallery_id INTEGER NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , title VARCHAR(120) NOT NULL COLLATE BINARY, content CLOB NOT NULL COLLATE BINARY)');
        $this->addSql('CREATE INDEX IDX_876E0D94E7AF8F ON photos (gallery_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(45) NOT NULL COLLATE BINARY)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_data_id INTEGER NOT NULL, email VARCHAR(180) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , password VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('CREATE UNIQUE INDEX email_idx ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E96FF8BF36 ON users (user_data_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE users_data (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(128) DEFAULT NULL COLLATE BINARY, firstname VARCHAR(128) DEFAULT NULL COLLATE BINARY)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE comments');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE galleries');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE photo_tag');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE photos');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE tags');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE users');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE users_data');
    }
}
