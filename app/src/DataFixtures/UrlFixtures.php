<?php

/**
 * Url Fixtures
 */

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\Url;
use DateMalformedStringException;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Random\RandomException;

/**
 * Klasa UrlFixtures
 *
 * Tworzy losowe encje Url i przypisuje im losowe tagi.
 */
class UrlFixtures extends Fixture
{
    /**
     * Ładuje dane fikcyjne dla encji Url.
     *
     * @param ObjectManager $manager Menedżer encji Doctrine.
     *
     * @throws DateMalformedStringException
     * @throws RandomException
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $tags = $manager->getRepository(Tag::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $url = new Url();
            $url->setOriginalUrl($faker->url());
            $url->setShortCode(substr(md5(uniqid()), 0, 6));
            $url->setEmail($faker->email());
            $url->setClicks(random_int(0, 100));
            $url->setCreatedAt(new DateTimeImmutable('-'.random_int(0, 30).' days'));

            // Przypisanie losowego taga, jeśli dostępne
            if ($tags) {
                $url->setTag($tags[array_rand($tags)]);
            }

            $manager->persist($url);
        }

        $manager->flush();
    }
}
