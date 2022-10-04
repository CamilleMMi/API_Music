<?php

namespace App\Controller;

use Serializable;
use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Generator\UrlGenerator;

class GenreController extends AbstractController
{
    #[Route('/genre', name: 'app_genre')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/GenreController.php',
        ]);
    }

    /**
     * Return all the existing genres
     *
     * @param GenreRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/genres', name: 'all.genres', methods: ['GET'])]
    public function getAllGenre(GenreRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $genre = $repository->findAll();
        $jsonGenre = $serializer->serialize($genre, 'json', ["groups" => "getAllGenres"]);

        return new JsonResponse($jsonGenre, Response::HTTP_OK, [], true);
    }

    /**
     * Return a Genre depending on the id given
     * 
     * @param GenreRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/genre/{idGenre}', name:'genre.get', methods: ['GET'])]
    #[ParamConverter("genre", options : ["id"=> "idGenre"])]
    public function getGenreById(Genre $genre, SerializerInterface $serializer): JsonResponse
    {
        $jsonGenre = $serializer->serialize($genre, 'json', ["groups" => "getGenre"]);

        return new JsonResponse($jsonGenre, Response::HTTP_OK, [], true);
    }

    /**
     * Remove entirely a genre depending on the id given
     *
     * @param Genre $genre
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/genre/{idGenre}/remove', name: 'genre.deleteData', methods: ['DELETE'])]
    #[ParamConverter("genre", options : ["id" => "idGenre"])]
    public function deleteDataGenre(EntityManagerInterface $entityManager, Genre $genre, SerializerInterface $serializer): JsonResponse
    {
        $entityManager->remove($genre);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Set the status of the given genre to false
     * 
     * @param EntityManagerInterface $entityManager
     * @param Genre $genre
     * @return JsonResponse
     */
    #[Route('/api/genre/{idGenre}', name: 'genre.deleteStatus', methods: ['DELETE'])]
    #[ParamConverter("genre", options : ["id" => "idGenre"])]
    public function deleteGenre(EntityManagerInterface $entityManager, Genre $genre): JsonResponse
    {
        $genre->setStatus(false);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK, [], false);
    }

    #[Route('/api/genre', name: 'genre.create', methods: ['POST'])]
    public function createGenre(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $genre = $serializer->deserialize(
            $request->getContent(),
            Genre::class,
            "json"
        );
        $genre->setStatus(true);

        // $content = $request->toArray();
        // rajouter le repo de l'entité concerné
        // $trucatrouver = $repoEnquestion->find($content['idDutruc'] ?? -1);
        // $genre->setAuthor($trucatrouver);

        $error = $validator->validate($genre);
        if($error->count() > 0) {
            return new JsonResponse($serializer->serialize($error, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($genre);
        $entityManager->flush();

        $jsonGenre = $serializer->serialize($genre, "json", ['groups' => 'getGenre']);
        $location = $urlGenerator->generate('genre.get', ['idGenre' => $genre->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonGenre, Response::HTTP_CREATED, ["location" => $location], true);
    }

    #[Route('/api/genre/{idGenre}', name: 'genre.update', methods: ['PUT'])]
    #[ParamConverter("genre", options: ["id" => "idGenre"])]
    public function updateGenre(Genre $genre, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $genreUpdate = $serializer->deserialize(
            $request->getContent(),
            Genre::class,
            "json",
            [AbstractNormalizer::OBJECT_TO_POPULATE => $genre]
        );
        $genreUpdate->setStatus(true);
        $entityManager->persist($genreUpdate);
        $entityManager->flush();

        $jsonGenre = $serializer->serialize($genreUpdate, 'json', ['groups' => 'getGenre']);
        $location = $urlGenerator->generate('genre.get', ['idGenre' => $genreUpdate->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonGenre, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}