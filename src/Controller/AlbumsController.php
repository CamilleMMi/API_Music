<?php

namespace App\Controller;

use DateTime;
use App\Entity\Albums;
use Doctrine\ORM\EntityManager;
use App\Repository\AlbumsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

    #[Route('/api/albums/{idAlbums}/remove', name: 'albums.delete', methods: ["DELETE"])]
    #[ParamConverter("albums", options : ["id" => "idAlbums"])]
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

    #[Route('/api/albums', name: 'albums.create', methods: ["POST"])]
    #[IsGranted("ADMIN", message:'LOL')]
    public function createAlbums(Request $request, AlbumsRepository $repository,EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $albums = $serializer->deserialize(
            $request->getContent(),
            Albums::class,
            'json'
        );
        $albums->setStatus(true);

        $content = $request->toArray();
        $music = $repository->find($content["idMusic"] ?? -1);
        $albums->setMusics($music);

        $entityManager->persist($albums);
        $entityManager->flush();

        $jsonAlbums = $serializer->serialize($albums, 'json', ['groups' => "getAlbums"]);
        
        $location = $urlGenerator->generate('albums.get', ['idAlbums' => $albums->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        return new JsonResponse($jsonAlbums, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/albums/{idAlbums}', name: 'albums.update', methods: ["PUT"])]
    #[ParamConverter("albums", options : ["id" => "idAlbums"])]
    public function updateAlbums(Albums $albums, Request $request, AlbumsRepository $repository,EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatealbums = $serializer->deserialize(
            $request->getContent(),
            Albums::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $albums]
        );
        $updatealbums->setStatus(true);
        

        $content = $request->toArray();
        $music = $repository->find($content["idMusic"] ?? -1);
        $updatealbums->setMusics($music);

        $errors = $validator->validate($albums);
        if($errors->count() > 0)
        {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $entityManager->persist($albums);
        $entityManager->flush();
        
        $jsonAlbums = $serializer->serialize($albums, 'json', ['groups' => "getAlbums"]);
        
        $location = $urlGenerator->generate('albums.get', ['idAlbums' => $albums->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        return new JsonResponse($jsonAlbums, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    
}
