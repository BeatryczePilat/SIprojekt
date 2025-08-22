<?php

/**
 * Url Fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\Url;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Random\RandomException;

/**
 * Klasa UrlFixtures.
 */
class UrlFixtures extends Fixture
{
    /**
     * Ładuje dane fikcyjne dla encji Url.
     *
     * @param ObjectManager $manager Menedżer encji Doctrine
     *
     * @return void void
     *
     * @throws \DateMalformedStringException DateMalformed String Exception
     * @throws RandomException               Random Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $tags = $manager->getRepository(Tag::class)->findAll();

        for ($i = 0; $i < 10; ++$i) {
            $url = new Url();
            $url->setOriginalUrl($faker->url());
            $url->setShortCode(substr(md5(uniqid()), 0, 6));
            $url->setEmail($faker->email());
            $url->setClicks(random_int(0, 100));
            $url->setCreatedAt(new \DateTimeImmutable('-'.random_int(0, 30).' days'));

            // Przypisanie losowego taga, jeśli dostępne
            if ($tags) {
                $url->setTag($tags[array_rand($tags)]);
            }

            $manager->persist($url);
        }

        $manager->flush();
    }
}
