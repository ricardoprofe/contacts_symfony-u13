<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130191454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(5) DEFAULT \'Mr.\' NOT NULL, name VARCHAR(100) NOT NULL, surname VARCHAR(100) DEFAULT NULL, birthdate DATE NOT NULL, email VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone (number VARCHAR(20) NOT NULL, id_contact_id INT NOT NULL, type VARCHAR(10) DEFAULT \'Mobile\' NOT NULL, INDEX IDX_444F97DD422BA59D (id_contact_id), PRIMARY KEY(id_contact_id, number)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DD422BA59D FOREIGN KEY (id_contact_id) REFERENCES contact (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DD422BA59D');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE phone');
    }
}
