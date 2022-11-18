<?php

namespace App\Controller;

use App\Entity\Authors;
use JMS\Serializer\Serializer;
use App\Repository\AlbumsRepository;
use App\Repository\AuthorsRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AuthorController extends AbstractController
{

    #[Route('/authors', name: 'app_author')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthorsController.php',
        ]);
    }

    /**
     * Return all the authors existing in the data base
     *
     * @param AuthorsRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/authors', name: 'authors.getAll', methods: ["GET"])]
    public function getAllAuthors(Request $request, AuthorsRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $authors = $repository->findWithPagination($page, $limit);
        $context = SerializationContext::create()->setGroups(['getAllAuthors']);
        $jsonAuthors = $serializer->serialize($authors, 'json', $context);
        return new JsonResponse($jsonAuthors, Response::HTTP_OK, [], true);
    }

    /**
     * Return an album depending on the id given
     * 
     * @param Authors $authors
     * @param SerializerInterface $serializer
     */
    #[Route('/api/authors/{idAuthors}', name: 'authors.get', methods: ['GET'])]
    #[ParamConverter("authors",options : ['id' => 'idAuthors'])]
    public function getAuthors(Authors $authors, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['getAllAuthors']);
        $jsonAuthors = $serializer->serialize($authors, 'json', $context);
        return new JsonResponse($jsonAuthors, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    /**
     * Remove entirely an album depending on the id given
     *
     * @param Authors $authors
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    // #[Route('/api/authors/{idAuthors}/remove', name: 'authors.delete', methods: ["DELETE"])]
    #[ParamConverter("authors", options : ["id" => "idAuthors"])]
    public function deleteAuthors(Authors $authors, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($authors);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Set the status of the given album to false
     * 
     * @param EntityManagerInterface $entityManager
     * @param Authors $authors
     * @return JsonResponse
     */
    #[Route('/api/authors/{idAuthors}', name: 'authors.turnOffStatus', methods: ["DELETE"])]
    #[ParamConverter("authors", options : ["id" => "idAuthors"])]
    public function turnOffAuthors(Authors $authors, EntityManagerInterface $entityManager): JsonResponse
    {
        $authors->setStatus(false);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_OK, [], false);
    }

    /**
     * Set the status of the given album to true
     * 
     * @param EntityManagerInterface $entityManager
     * @param Authors $authors
     * @return JsonResponse
     */
    #[Route('/api/authors/{idAuthors}', name: 'authors.turnOnStatus', methods: ['DELETE'])]
    #[IsGranted('ADMIN', message:'You do not have the correct role to access this service')]
    #[ParamConverter("authors", options : ["id" => "idAuthors"])]
    public function turnOnAuthors(EntityManagerInterface $entityManager, Authors $authors): JsonResponse
    {
        $authors->setStatus(true);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK, [], false);
    }

    /**
     * Create an album with the given datas
     *
     * @param Request $request
     * @param AuthorsRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/authors', name: 'authors.create', methods: ["POST"])]
    public function createAuthors(Request $request, AuthorsRepository $repository,EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $authors = $serializer->deserialize(
            $request->getContent(),
            Authors::class,
            'json'
        );
        $authors->setStatus(true);

        $content = $request->toArray();
        $music = $repository->find($content["idMusic"] ?? -1);
        $authors->setMusics($music);

        $entityManager->persist($authors);
        $entityManager->flush();

        $jsonAuthors = $serializer->serialize($authors, 'json');
        
        $location = $urlGenerator->generate('authors.get', ['idAuthors' => $authors->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        return new JsonResponse($jsonAuthors, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * Update the album referenced by the id given
     * 
     * @param Request $request
     * @param AuthorsRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param Authors $authors
     * @param ValidatorInterface $validator
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/authors/{idAuthors}', name: 'authors.update', methods: ["PUT"])]
    #[ParamConverter("authors", options : ["id" => "idAuthors"])]
    public function updateAuthors(Authors $authors, Request $request, AuthorsRepository $repository, AlbumsRepository $albumRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updateauthor = $serializer->deserialize(
            $request->getContent(),
            Authors::class,
            'json',
        );

        $content = $request->toArray();
        $authors->setName($updateauthor->getName() ?? $authors->getName());
        $authors->setStatus(true);

        if(array_key_exists('idAuthors', $content) && $content["idAuthors"] && isset($content['idAuthors'])) {
            dd($authors->addAlbumsid($albumRepository->find($updateauthor->getAlbumsid())));
            
            dd($content);
            dd($authors->getAlbumsid());
        }

        $entityManager->persist($authors);
        $entityManager->flush();

        $content = $request->toArray();

        $errors = $validator->validate($authors);
        if($errors->count() > 0)
        {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $entityManager->persist($authors);
        $entityManager->flush();
        
        $jsonAuthors = $serializer->serialize($authors, 'json');
        
        $location = $urlGenerator->generate('authors.get', ['idAuthors' => $authors->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        return new JsonResponse($jsonAuthors, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
