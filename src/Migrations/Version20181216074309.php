<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181216074309 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payement ADD payement_paid TINYINT(1) NOT NULL, CHANGE family_name family_name VARCHAR(255) NOT NULL, CHANGE city city VARCHAR(255) NOT NULL, CHANGE code_postal code_postal INT NOT NULL, CHANGE credit_card_number credit_card_number INT NOT NULL, CHANGE code_security code_security INT NOT NULL, CHANGE cb_expire cb_expire DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payement DROP payement_paid, CHANGE family_name family_name VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE city city VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE code_postal code_postal INT DEFAULT NULL, CHANGE credit_card_number credit_card_number INT DEFAULT NULL, CHANGE code_security code_security INT DEFAULT NULL, CHANGE cb_expire cb_expire DATETIME DEFAULT NULL');
    }
}
