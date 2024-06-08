<?php
namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Feed;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $feedRepository = $manager->getRepository(Feed::class);
        $userRepository = $manager->getRepository(User::class);

        $feeds = $feedRepository->findAll();
        $users = $userRepository->findAll();

        foreach ($feeds as $feed) {
            $numComments = mt_rand(0, 5);
            for ($i = 0; $i < $numComments; $i++) {
                $comment = new Comment();
                $comment->setContent($faker->sentence(10));
                $comment->setFeed($feed);
                $comment->setUserCommented($faker->randomElement($users));

                $manager->persist($comment);
                $feed->addCommentss($comment); // Assurez-vous que cette méthode existe et s'appelle addComment
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FeedFixtures::class,
            UserFixtures::class,
        ];
    }
}
