<?php

namespace App\DataFixtures;

use App\Entity\Lab;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LabFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $items = [
            [
                'title' => 'Intro to Linux',
                'slug' => 'intro-to-linux',
                'description' => 'Learn Linux basics: files, permissions, users, processes, and essential commands.',
                'difficulty' => 'Easy',
                'points' => 50,
                'flag' => 'CYBERLAB{linux_basics}',
                'published' => true,
            ],
            [
                'title' => 'Networking Fundamentals',
                'slug' => 'networking-fundamentals',
                'description' => 'Understand IP addressing, subnetting, DNS, HTTP, ports, and troubleshooting tools.',
                'difficulty' => 'Easy',
                'points' => 60,
                'flag' => 'CYBERLAB{net_basics}',
                'published' => true,
            ],
            [
                'title' => 'Web Recon & OSINT',
                'slug' => 'web-recon-osint',
                'description' => 'Practice recon techniques: subdomains, directories, metadata, and OSINT basics.',
                'difficulty' => 'Medium',
                'points' => 120,
                'flag' => 'CYBERLAB{osint_recon}',
                'published' => true,
            ],
            [
                'title' => 'SQL Injection Basics',
                'slug' => 'sql-injection-basics',
                'description' => 'Identify and exploit basic SQL injection vulnerabilities safely.',
                'difficulty' => 'Medium',
                'points' => 150,
                'flag' => 'CYBERLAB{sqli_basic}',
                'published' => true,
            ],
            [
                'title' => 'Privilege Escalation (Linux)',
                'slug' => 'privilege-escalation-linux',
                'description' => 'Explore common Linux privesc vectors: SUID, misconfigs, sudo, services.',
                'difficulty' => 'Hard',
                'points' => 250,
                'flag' => 'CYBERLAB{privesc_linux}',
                'published' => true,
            ],
        ];

        foreach ($items as $data) {
            $lab = new Lab();
            $lab->setTitle($data['title']);
            $lab->setSlug($data['slug']);
            $lab->setDescription($data['description']);
            $lab->setDifficulty($data['difficulty']);
            $lab->setPoints($data['points']);
            $lab->setFlag($data['flag']);
            $lab->setPublished($data['published']);
            $lab->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($lab);
        }

        $manager->flush();
    }
}
