<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115090144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__coaster AS SELECT id, park_id, author_id, name, max_speed, length, max_height, operating, published FROM coaster');
        $this->addSql('DROP TABLE coaster');
        $this->addSql('CREATE TABLE coaster (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, park_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL, name VARCHAR(80) NOT NULL, max_speed INTEGER DEFAULT NULL, length INTEGER DEFAULT NULL, max_height INTEGER NOT NULL, operating BOOLEAN NOT NULL, published BOOLEAN DEFAULT NULL, CONSTRAINT FK_F6312A7844990C25 FOREIGN KEY (park_id) REFERENCES park (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F6312A78F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO coaster (id, park_id, author_id, name, max_speed, length, max_height, operating, published) SELECT id, park_id, author_id, name, max_speed, length, max_height, operating, published FROM __temp__coaster');
        $this->addSql('DROP TABLE __temp__coaster');
        $this->addSql('CREATE INDEX IDX_F6312A78F675F31B ON coaster (author_id)');
        $this->addSql('CREATE INDEX IDX_F6312A7844990C25 ON coaster (park_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__coaster AS SELECT id, park_id, author_id, name, max_speed, length, max_height, operating, published FROM coaster');
        $this->addSql('DROP TABLE coaster');
        $this->addSql('CREATE TABLE coaster (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, park_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL, name VARCHAR(80) NOT NULL, max_speed INTEGER DEFAULT NULL, length INTEGER DEFAULT NULL, max_height INTEGER DEFAULT NULL, operating BOOLEAN NOT NULL, published BOOLEAN DEFAULT NULL, CONSTRAINT FK_F6312A7844990C25 FOREIGN KEY (park_id) REFERENCES park (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F6312A78F675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO coaster (id, park_id, author_id, name, max_speed, length, max_height, operating, published) SELECT id, park_id, author_id, name, max_speed, length, max_height, operating, published FROM __temp__coaster');
        $this->addSql('DROP TABLE __temp__coaster');
        $this->addSql('CREATE INDEX IDX_F6312A7844990C25 ON coaster (park_id)');
        $this->addSql('CREATE INDEX IDX_F6312A78F675F31B ON coaster (author_id)');
    }
}
