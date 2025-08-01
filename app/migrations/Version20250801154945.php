<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250801154945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url_tag DROP FOREIGN KEY FK_56B26711BAD26311');
        $this->addSql('ALTER TABLE url_tag DROP FOREIGN KEY FK_56B2671181CFDAE7');
        $this->addSql('DROP TABLE url_tag');
        $this->addSql('ALTER TABLE url ADD tag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE url ADD CONSTRAINT FK_F47645AEBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_F47645AEBAD26311 ON url (tag_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE url_tag (url_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_56B2671181CFDAE7 (url_id), INDEX IDX_56B26711BAD26311 (tag_id), PRIMARY KEY(url_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE url_tag ADD CONSTRAINT FK_56B26711BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE url_tag ADD CONSTRAINT FK_56B2671181CFDAE7 FOREIGN KEY (url_id) REFERENCES url (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE url DROP FOREIGN KEY FK_F47645AEBAD26311');
        $this->addSql('DROP INDEX IDX_F47645AEBAD26311 ON url');
        $this->addSql('ALTER TABLE url DROP tag_id');
    }
}
