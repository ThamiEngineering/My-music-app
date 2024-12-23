<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use App\Repository\ArtisteRepository;
use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ArtisteRepository $artisteRepository, AlbumRepository $albumRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $artistes = $artisteRepository->findAll();
        $albums = $albumRepository->findAll();

        return $this->render('base.html.twig', [
            'artistes' => $artistes,
            'albums' => $albums,
        ]);
    }
}
