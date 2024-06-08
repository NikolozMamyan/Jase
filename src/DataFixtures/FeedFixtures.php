<?php
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Feed;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FeedFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $userRepository = $manager->getRepository(User::class);

        $users = $userRepository->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < mt_rand(1, 5); $i++) {
                $feed = new Feed();
                $feed->setTitle($faker->sentence(6));
                $feed->setDescription($faker->paragraph(4));
                $feed->setAuthor($user);
                $feed->setShared($faker->numberBetween(0, 100));
                $feed->setImageName($faker->imageUrl());

                $manager->persist($feed);
            }
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
