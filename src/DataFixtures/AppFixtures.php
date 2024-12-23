<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Artiste;
use App\Entity\Album;
use App\Entity\Playlist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un administrateur
        $admin = new User();
        $admin->setEmail('admin@exemple.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('Master');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'secure_password'));
        $manager->persist($admin);

        // Créer des utilisateurs réguliers et bannis
        $usersData = [
            ['email' => 'utilisateur1@exemple.com', 'firstName' => 'John', 'lastName' => 'Doe', 'roles' => ['ROLE_USER']],
            ['email' => 'utilisateur2@exemple.com', 'firstName' => 'Jane', 'lastName' => 'Doe', 'roles' => ['ROLE_USER']],
            ['email' => 'utilisateur3@exemple.com', 'firstName' => 'Jim', 'lastName' => 'Beam', 'roles' => ['IS_BANNED']],
            ['email' => 'utilisateur4@exemple.com', 'firstName' => 'Jack', 'lastName' => 'Daniels', 'roles' => ['IS_BANNED']],
            ['email' => 'utilisateur5@exemple.com', 'firstName' => 'Johnny', 'lastName' => 'Walker', 'roles' => ['IS_BANNED']],
        ];

        $users = [];
        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setRoles($userData['roles']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'secure_password'));
            $manager->persist($user);
            $users[] = $user;
        }

        // Créer des artistes, albums et playlists
        $artistesNoms = ['Lana Del Rock', 'DJ Mystic', 'The Codebreakers', 'Echo Harmonies', 'Neo Classics'];
        $playlistsNoms = ['Mood Booster', 'Chill Vibes', 'Party Hits', 'Workout Mix', 'Study Focus'];

        foreach ($artistesNoms as $index => $nomArtiste) {
            $artiste = new Artiste();
            $artiste->setNom($nomArtiste);
            $artiste->setBiographie("Biographie de $nomArtiste, un artiste renommé.");
            $artiste->setDateDebut(new \DateTime('-' . (10 + $index) . ' years'));
            $artiste->setImage('https://via.placeholder.com/150');
            $manager->persist($artiste);

            $albums = [];
            for ($j = 1; $j <= 3; $j++) {
                $album = new Album();
                $album->setTitre("Album $j de $nomArtiste");
                $album->setDescription("Description de l'album $j de $nomArtiste.");
                $album->setDateSortie(new \DateTime('-' . ($j * 2) . ' years'));
                $album->setArtiste($artiste);
                $album->setCover('https://via.placeholder.com/150');
                $manager->persist($album);
                $albums[] = $album;
            }

            // Créer des playlists pour chaque utilisateur avec plusieurs albums
            foreach ($users as $user) {
                $playlist = new Playlist();
                $playlist->setNom($playlistsNoms[array_rand($playlistsNoms)]);
                $playlist->setDescription("Une playlist contenant plusieurs albums de $nomArtiste.");
                $playlist->setUtilisateur($user);
                foreach ($albums as $album) {
                    $playlist->addAlbum($album);
                }
                $manager->persist($playlist);
            }
        }

        $manager->flush();
    }
}
