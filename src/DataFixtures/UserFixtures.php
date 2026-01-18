<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // ADMIN
        $admin = new User();
        $admin->setEmail('admin@cyberlab.com');
        $admin->setFullName('CyberLab Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // TEACHER
        $teacher = new User();
        $teacher->setEmail('teacher@cyberlab.com');
        $teacher->setFullName('CyberLab Teacher');
        $teacher->setRoles(['ROLE_TEACHER']);
        $teacher->setIsActive(true);
        $teacher->setPassword($this->hasher->hashPassword($teacher, 'teacher123'));
        $manager->persist($teacher);

        $manager->flush();
    }
}
