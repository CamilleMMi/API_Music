<?php

namespace App\Controller;

use App\Entity\Albums;
use App\Repository\AlbumsRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/api/albums', name: 'albums.getAll', methods: ["GET"])]
    public function getAllAlbums(AlbumsRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $albums = $repository->findAll();
        $jsonAlbums = $serializer->serialize($albums, 'json', ['groups' => 'getAllAlbums']);
        return new JsonResponse($jsonAlbums, Response::HTTP_OK, [], true);
    }

    #[Route('/api/albums/{idAlbums}', name: 'albums.get', methods: ['GET'])]
    #[ParamConverter("albums",options : ['id' => 'idAlbums'])]
    public function getAlbums(Albums $albums, AlbumsRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $jsonAlbums = $serializer->serialize($albums, 'json');
        return new JsonResponse($jsonAlbums, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/albums/delete/{idAlbums}', name: 'albums.delete', methods: ["DELETE"])]
    #[ParamConverter("albums", option : ["id" => "idAlbums"])]
    public function deleteAlbums2(Albums $albums, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($albums);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/albums/{idAlbums}', name: 'albums.deleteStatus', methods: ["DELETE"])]
    #[ParamConverter("albums", options : ["id" => "idAlbums"])]
    public function deleteAlbums(Albums $albums, EntityManagerInterface $entityManager): JsonResponse
    {
        $albums->setStatus(false);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_OK, [], false);
    }
    
}
