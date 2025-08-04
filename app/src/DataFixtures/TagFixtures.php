<?php
/**
 * Tag Fixtures
 */
namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Klasa TagFixtures
 *
 * Tworzy losowe encje Tag z wykorzystaniem biblioteki Faker.
 */
class TagFixtures extends Fixture
{
    /**
     * Ładuje dane fikcyjne dla encji Tag.
     *
     * @param ObjectManager $manager Menedżer encji Doctrine.
     */
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();
        $usedNames = [];

        for ($i = 0; $i < 10; $i++) {
            // Ensure unique tag names
            do {
                $name = ucfirst($faker->unique()->word());
            } while (in_array($name, $usedNames, true));

            $usedNames[] = $name;

            $tag = new Tag();
            $tag->setName($name);
            $tag->setSlug(strtolower($name));

            $manager->persist($tag);
        }

        $manager->flush();
    }
}
