<?php

/**
 * Admin Fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AdminFixtures.
 */
class AdminFixtures extends Fixture
{
    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher Usługa do haszowania haseł użytkowników
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Ładuje dane fikcyjne dla encji Admin.
     *
     * @param ObjectManager $manager ObjectManager
     *
     * @return void void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        $admin = new Admin();
        $admin->setEmail('admin@example.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setNickname($faker->userName());

        $manager->persist($admin);
        $manager->flush();
    }
}
