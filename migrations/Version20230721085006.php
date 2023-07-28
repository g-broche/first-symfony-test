<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230721085006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name_category VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distributors (id INT AUTO_INCREMENT NOT NULL, name_distributor VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, reference_id_id INT NOT NULL, category_id_id INT NOT NULL, name_product VARCHAR(255) NOT NULL, description_product LONGTEXT NOT NULL, price_product DOUBLE PRECISION NOT NULL, availability_product TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_B3BA5A5AA9E5FE47 (reference_id_id), INDEX IDX_B3BA5A5A9777D11E (category_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_distributors (products_id INT NOT NULL, distributors_id INT NOT NULL, INDEX IDX_5B88DC586C8A81A9 (products_id), INDEX IDX_5B88DC5882ECE2D9 (distributors_id), PRIMARY KEY(products_id, distributors_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `references` (id INT AUTO_INCREMENT NOT NULL, number_reference VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AA9E5FE47 FOREIGN KEY (reference_id_id) REFERENCES `references` (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A9777D11E FOREIGN KEY (category_id_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE products_distributors ADD CONSTRAINT FK_5B88DC586C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_distributors ADD CONSTRAINT FK_5B88DC5882ECE2D9 FOREIGN KEY (distributors_id) REFERENCES distributors (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AA9E5FE47');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A9777D11E');
        $this->addSql('ALTER TABLE products_distributors DROP FOREIGN KEY FK_5B88DC586C8A81A9');
        $this->addSql('ALTER TABLE products_distributors DROP FOREIGN KEY FK_5B88DC5882ECE2D9');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE distributors');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE products_distributors');
        $this->addSql('DROP TABLE `references`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
