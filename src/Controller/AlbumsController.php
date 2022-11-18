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
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

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

    /**
     * Return all the albums existing in the data base
     *
     * @param AlbumsRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/albums', name: 'albums.getAll', methods: ["GET"])]
    // #[IsGranted('ADMIN')]
    public function getAllAlbums(Request $request, AlbumsRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $albums = $repository->findWithPagination($page, $limit);
        $context = SerializationContext::create()->setGroups(['getAllAlbums']);
        $jsonAlbums = $serializer->serialize($albums, 'json', $context);
        return new JsonResponse($jsonAlbums, Response::HTTP_OK, [], true);
    }

    //jsonCours = $cache->get("getAllCOurses", function (ItemInterface $item ) use ($repository, $serializer)) {
    //    $item->tag("CoursCache");
    //    
    //    echo "Mise en Cache";

    //    $coyyurs = function
    //}

    /**
     * Return an album depending on the id given
     * 
     * @param Albums $albums
     * @param 
     */
    #[Route('/api/albums/{idAlbums}', name: 'albums.get', methods: ['GET'])]
    #[ParamConverter("albums",options : ['id' => 'idAlbums'])]
    public function getAlbums(Albums $albums, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['getAlbums']);
        $jsonAlbums = $serializer->serialize($albums, 'json', $context);
        return new JsonResponse($jsonAlbums, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    /**
     * Remove entirely an album depending on the id given
     *
     * @param Albums $albums
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    // #[Route('/api/albums/{idAlbums}/remove', name: 'albums.delete', methods: ["DELETE"])]
    #[ParamConverter("albums", options : ["id" => "idAlbums"])]
    public function deleteAlbums(Albums $albums, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($albums);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Set the status of the given album to false
     * 
     * @param EntityManagerInterface $entityManager
     * @param Albums $albums
     * @return JsonResponse
     */
    #[Route('/api/albums/{idAlbums}', name: 'albums.turnOffStatus', methods: ["DELETE"])]
    #[ParamConverter("albums", options : ["id" => "idAlbums"])]
    public function turnOffAlbums(Albums $albums, EntityManagerInterface $entityManager): JsonResponse
    {
        $albums->setStatus(false);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_OK, [], false);
    }

    /**
     * Set the status of the given album to true
     * 
     * @param EntityManagerInterface $entityManager
     * @param Albums $albums
     * @return JsonResponse
     */
    #[Route('/api/albums/{idAlbums}', name: 'albums.turnOnStatus', methods: ['DELETE'])]
    // #[IsGranted('ADMIN', message:'You do not have the correct role to access this service')]
    #[ParamConverter("albums", options : ["id" => "idAlbums"])]
    public function turnOnAlbums(EntityManagerInterface $entityManager, Albums $albums): JsonResponse
    {
        $albums->setStatus(true);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK, [], false);
    }

    /**
     * Create an album with the given datas
     *
     * @param Request $request
     * @param AlbumsRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/albums', name: 'albums.create', methods: ["POST"])]
    // #[IsGranted("ADMIN", message:'You do not have the correct role to access this service')]
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

        $context = SerializationContext::create()->setGroups(['getAlbums']);
        $jsonAlbums = $serializer->serialize($albums, 'json', $context);
        
        $location = $urlGenerator->generate('albums.get', ['idAlbums' => $albums->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        return new JsonResponse($jsonAlbums, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * Update the album referenced by the id given
     * 
     * @param Request $request
     * @param AlbumsRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param Albums $albums
     * @param ValidatorInterface $validator
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/albums/{idAlbums}', name: 'albums.update', methods: ["PUT"])]
    #[ParamConverter("albums", options : ["id" => "idAlbums"])]
    public function updateAlbums(Albums $albums, Request $request, AlbumsRepository $repository,EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatealbums = $serializer->deserialize(
            $request->getContent(),
            Albums::class,
            'json',
        );
        $content = $request->toArray();

        $updatealbums->setDate($updatealbums->getDate() ?? $albums->getDate());
        $updatealbums->setName($updatealbums->getName() ?? $albums->getName());
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
        
        $context = SerializationContext::create()->setGroups(['getAlbums']);
        $jsonAlbums = $serializer->serialize($albums, 'json', $context);
        
        $location = $urlGenerator->generate('albums.get', ['idAlbums' => $albums->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        return new JsonResponse($jsonAlbums, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    
}
