<?php

namespace App\Controller;

use App\Entity\Artiste;
use App\Entity\Album;
use App\Entity\User;
use App\Repository\ArtisteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(UserRepository $userRepository, ArtisteRepository $artisteRepository): Response
    {
        $users = $userRepository->findByRole('ROLE_USER');
        $bannedUsers = $userRepository->findByRole('IS_BANNED');
        $artistes = $artisteRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
            'bannedUsers' => $bannedUsers,
            'artistes' => $artistes,
        ]);
    }

    #[Route('/admin/ban/{id}', name: 'admin_ban_user', methods: ['POST'])]
    public function banUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setRoles(['IS_BANNED']);
        $entityManager->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/unban/{id}', name: 'admin_unban_user', methods: ['POST'])]
    public function unbanUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setRoles(['ROLE_USER']);
        $entityManager->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/create-artist', name: 'admin_create_artist', methods: ['POST'])]
    public function createArtist(Request $request, EntityManagerInterface $entityManager): Response
    {
        $artiste = new Artiste();
        $artiste->setNom($request->request->get('nom'));
        $artiste->setBiographie($request->request->get('biographie'));
        $artiste->setImage($request->request->get('image'));
        $artiste->setDateDebut(new \DateTime());

        $entityManager->persist($artiste);
        $entityManager->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/delete-artist/{id}', name: 'admin_delete_artist', methods: ['POST'])]
    public function deleteArtist(Artiste $artiste, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($artiste);
        $entityManager->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/add-album/{artisteId}', name: 'admin_add_album', methods: ['POST'])]
    public function addAlbum(Request $request, Artiste $artiste, EntityManagerInterface $entityManager): Response
    {
        $album = new Album();
        $album->setTitre($request->request->get('titre'));
        $album->setDescription($request->request->get('description'));
        $album->setCover($request->request->get('cover'));
        $album->setDateSortie(new \DateTime());
        $album->setArtiste($artiste);

        $entityManager->persist($album);
        $entityManager->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/delete-album/{id}', name: 'admin_delete_album', methods: ['POST'])]
    public function deleteAlbum(Album $album, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($album);
        $entityManager->flush();

        return $this->redirectToRoute('admin_dashboard');
    }
}
