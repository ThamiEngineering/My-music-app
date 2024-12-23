<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Playlist;
use App\Repository\AlbumRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistController extends AbstractController
{
    #[Route('/add-to-playlist', name: 'add_to_playlist', methods: ['POST'])]
    public function addToPlaylist(Request $request, AlbumRepository $albumRepository, PlaylistRepository $playlistRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $albumId = $request->request->get('albumId');
        $playlistId = $request->request->get('playlistId');

        $album = $albumRepository->find($albumId);
        $playlist = $playlistRepository->find($playlistId);

        if (!$album || !$playlist || $playlist->getUtilisateur() !== $user) {
            throw $this->createNotFoundException('Album or Playlist not found or you do not have access to this playlist');
        }

        $playlist->addAlbum($album);
        $entityManager->flush();

        return $this->redirectToRoute('homepage');
    }

    #[Route('/remove-from-playlist/{playlistId}/{albumId}', name: 'remove_from_playlist', methods: ['POST'])]
    public function removeFromPlaylist(int $playlistId, int $albumId, PlaylistRepository $playlistRepository, AlbumRepository $albumRepository, EntityManagerInterface $entityManager): Response
    {
        $playlist = $playlistRepository->find($playlistId);
        $album = $albumRepository->find($albumId);

        if (!$playlist || !$album) {
            throw $this->createNotFoundException('Playlist or Album not found');
        }

        $playlist->removeAlbum($album);
        $entityManager->flush();

        return $this->redirectToRoute('homepage');
    }

    #[Route('/create-playlist', name: 'create_playlist', methods: ['POST'])]
    public function createPlaylist(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $nom = $request->request->get('nom');
        $description = $request->request->get('description');

        $playlist = new Playlist();
        $playlist->setNom($nom);
        $playlist->setDescription($description);
        $playlist->setUtilisateur($user);

        $entityManager->persist($playlist);
        $entityManager->flush();

        return $this->redirectToRoute('homepage');
    }

    #[Route('/delete-playlist/{id}', name: 'delete_playlist', methods: ['POST'])]
    public function deletePlaylist(int $id, PlaylistRepository $playlistRepository, EntityManagerInterface $entityManager): Response
    {
        $playlist = $playlistRepository->find($id);

        if ($playlist && $playlist->getUtilisateur() === $this->getUser()) {
            $entityManager->remove($playlist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('homepage');
    }
}
