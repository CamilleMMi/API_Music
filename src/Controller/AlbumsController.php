<?php

namespace App\Controller;

use App\Entity\Albums;
use App\Repository\AlbumsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AlbumsController extends AbstractController
{
    #[Route('/albums', name: 'app_albums')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AlbumsController.php',
        ]);
    }

    #[Route('/api/albums', name: 'albums.getAll', methds: ["GET"])]
    public function getAllAlbums(AlbumsRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $albums = $repository->findAll();
        $jsonAlbums = $serializer->serialize($albums, 'json', ['groups' => 'getAllAlbums']);
        return new JsonResponse($jsonAlbums, Response::HTTP_OK, [], true);
    }
}
