<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180518174910 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, sonde BIGINT DEFAULT NULL, cargoI BIGINT DEFAULT NULL, cargoV BIGINT DEFAULT NULL, cargoX BIGINT DEFAULT NULL, colonizer INT DEFAULT NULL, recycleur INT DEFAULT NULL, barge INT DEFAULT NULL, hunter BIGINT DEFAULT NULL, hunterHeavy BIGINT DEFAULT NULL, corvet BIGINT DEFAULT NULL, corvetLaser BIGINT DEFAULT NULL, fregate BIGINT DEFAULT NULL, fregatePlasma BIGINT DEFAULT NULL, croiser BIGINT DEFAULT NULL, ironClad BIGINT DEFAULT NULL, destroyer BIGINT DEFAULT NULL, productAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_users ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028244584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C25028244584665A ON app_users (product_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028244584665A');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP INDEX UNIQ_C25028244584665A ON app_users');
        $this->addSql('ALTER TABLE app_users DROP product_id');
    }
}
