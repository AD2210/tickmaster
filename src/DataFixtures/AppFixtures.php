<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Ticket;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private ManagerRegistry $registry;
    private $faker;

    public function __construct(UserPasswordHasherInterface $hasher, ManagerRegistry $registry)
    {
        $this->hasher = $hasher;
        $this->registry = $registry;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //Créations de 15 users, 5 de chaque rôle
        $this->makeUsers($manager, 5);
        //Enregistrement en BDD
        $manager->flush();

        //Créations de 100 tickets avec status et priorités aléatoires
        $this->makeTickets ($manager, 100);
        //Enregistrement en BDD
        $manager->flush();
    }

    /**
     * Make some users, one for each role x $nb
     * @param ObjectManager $manager
     * @param int $nb
     * @return void
     */
    function makeUsers(ObjectManager $manager, int $nb): void
    {
        $nb = $nb == null ? 1 : $nb;
        for ($i = 0; $i < $nb; $i++) {
            $roles = ['ROLE_USER', 'ROLE_TECHNICIEN', 'ROLE_ADMIN'];
            foreach ($roles as $role) {
                $user = new User();
                $user->setFirstname($this->faker->firstName());
                $user->setName($this->faker->lastname());
                $user->setEmail(strtolower($user->getFirstname() .'.' .$user->getName()) . '@example.com');
                $user->setRole([$role]);
                $user->setPassword($this->hasher->hashPassword($user, 'password123'));
                $user->setSettings([
                    'theme' => 'light',
                    'showCharts' => true
                ]);
                $manager->persist($user);
            }
        }
    }

    function makeTickets(ObjectManager $manager, int $nb): void
    {
        $userRepo = $this->registry->getRepository(User::class);
        $users = $userRepo->findAll();

        $nb = $nb == null ? 1 : $nb;
        for ($i = 0; $i < $nb; $i++) {
            $ticket = new Ticket();
            $ticket->setTitle(ucfirst($this->faker->words(3, true)));
            $ticket->setDescription($this->faker->paragraphs(2, true));

            // Statut et priorité aléatoires
            $ticket->setStatus($this->faker->randomElement(Ticket::STATUSES));
            $ticket->setPriority($this->faker->randomElement(Ticket::PRIORITIES));

            // Dates aléatoires sur l'année écoulée
            $created = $this->faker->dateTimeBetween('-1 year', 'now')->setTime(0, 0);
            $ticket->setCreatedAt($created);
            $updated = $this->faker->dateTimeBetween($created, 'now')->setTime(0, 0);
            $ticket->setUpdatedAt($updated);

            // Responsable aléatoire
            $ticket->setOwner($this->faker->randomElement($users));

            $manager->persist($ticket);
        }
    }
}
