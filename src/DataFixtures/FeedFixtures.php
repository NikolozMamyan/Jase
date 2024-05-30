<?php

namespace App\DataFixtures;

use App\Entity\Feed;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FeedFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 20; $i++) {
            $feed = new Feed();
            $feed->setTitle($faker->sentence);
            $feed->setDescription($faker->paragraph);
            $feed->setLiked($faker->numberBetween(0, 100));
            $feed->setShared($faker->numberBetween(0, 50));
            $feed->setAuthor($faker->randomElement($users));

            $manager->persist($feed);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}