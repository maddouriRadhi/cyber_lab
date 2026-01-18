<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260118101209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lab (id INT AUTO_INCREMENT NOT NULL, teacher_id INT DEFAULT NULL, title VARCHAR(120) NOT NULL, description LONGTEXT NOT NULL, difficulty VARCHAR(20) NOT NULL, points INT NOT NULL, flag VARCHAR(255) NOT NULL, published TINYINT(1) NOT NULL, slug VARCHAR(160) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', scheduled_at DATETIME DEFAULT NULL, meeting_link VARCHAR(255) DEFAULT NULL, max_seats INT DEFAULT NULL, UNIQUE INDEX UNIQ_61D6B1C4989D9B62 (slug), INDEX IDX_61D6B1C441807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lab_enrollment (id INT AUTO_INCREMENT NOT NULL, lab_id INT NOT NULL, student_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B38F00FA628913D5 (lab_id), INDEX IDX_B38F00FACB944F1A (student_id), UNIQUE INDEX uniq_lab_student (lab_id, student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, status VARCHAR(20) NOT NULL, total_cents INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, order_ref_id INT NOT NULL, sku VARCHAR(60) NOT NULL, name VARCHAR(140) NOT NULL, unit_price_cents INT NOT NULL, qty INT NOT NULL, INDEX IDX_52EA1F09E238517C (order_ref_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE solve (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, lab_id INT NOT NULL, solved_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1E65F3B4A76ED395 (user_id), INDEX IDX_1E65F3B4628913D5 (lab_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teacher_application (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(140) NOT NULL, email VARCHAR(180) NOT NULL, primary_track VARCHAR(60) NOT NULL, experience_level VARCHAR(30) NOT NULL, bio LONGTEXT NOT NULL, links VARCHAR(255) DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, full_name VARCHAR(120) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, UNIQUE INDEX uniq_user_email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lab ADD CONSTRAINT FK_61D6B1C441807E1D FOREIGN KEY (teacher_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE lab_enrollment ADD CONSTRAINT FK_B38F00FA628913D5 FOREIGN KEY (lab_id) REFERENCES lab (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lab_enrollment ADD CONSTRAINT FK_B38F00FACB944F1A FOREIGN KEY (student_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09E238517C FOREIGN KEY (order_ref_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE solve ADD CONSTRAINT FK_1E65F3B4A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE solve ADD CONSTRAINT FK_1E65F3B4628913D5 FOREIGN KEY (lab_id) REFERENCES lab (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lab DROP FOREIGN KEY FK_61D6B1C441807E1D');
        $this->addSql('ALTER TABLE lab_enrollment DROP FOREIGN KEY FK_B38F00FA628913D5');
        $this->addSql('ALTER TABLE lab_enrollment DROP FOREIGN KEY FK_B38F00FACB944F1A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09E238517C');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE solve DROP FOREIGN KEY FK_1E65F3B4A76ED395');
        $this->addSql('ALTER TABLE solve DROP FOREIGN KEY FK_1E65F3B4628913D5');
        $this->addSql('DROP TABLE lab');
        $this->addSql('DROP TABLE lab_enrollment');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE solve');
        $this->addSql('DROP TABLE teacher_application');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
