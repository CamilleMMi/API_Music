<?php

namespace App\Controller;

use App\Entity\Music;
use Doctrine\ORM\EntityManager;
use App\Repository\MusicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MusicController extends AbstractController
{
    #[Route('/music', name: 'app_music')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MusicController.php',
        ]);
    }

    /**
     * Return all the existing musics
     *
     * @param MusicRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/musics', name: 'all.musics', methods: ['GET'])]
    public function getAllMusic(Request $request, MusicRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $music = $repository->findWithPagination($page, $limit);
        $context = SerializationContext::create()->setGroups(['getAllMusics']);
        $jsonMusic = $serializer->serialize($music, 'json', $context);

        return new JsonResponse($jsonMusic, Response::HTTP_OK, [], true);
    }
    
    /**
     * Return a random music
     *
     * @param Music $music
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/music/random', name: 'music.getRandom', methods: ['GET'])]
    public function getRandomMusic(MusicRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $music = $repository->findRandomMusic();
        $context = SerializationContext::create()->setGroups(['getMusic']);
        $jsonMusic = $serializer->serialize($music, 'json', $context);

        return new JsonResponse($jsonMusic, Response::HTTP_OK, [], true);
    }

    /**
     * Return a music depending on the id given
     *
     * @param Music $music
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/music/{idMusic}', name: 'music.getOne', methods: ['GET'])]
    #[ParamConverter("music", options : ["id" => "idMusic"])]
    public function getMusicById(Music $music, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['getMusic']);
        $jsonMusic = $serializer->serialize($music, 'json', $context);

        return new JsonResponse($jsonMusic, Response::HTTP_OK, [], true);
    }

    /**
     * Remove entirely a music depending on the id given
     *
     * @param Music $music
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/music/{idMusic}/remove', name: 'music.deleteData', methods: ['DELETE'])]
    #[ParamConverter("music", options : ["id" => "idMusic"])]
    public function deleteDataMusic(EntityManagerInterface $entityManager, Music $music, SerializerInterface $serializer): JsonResponse
    {
        $entityManager->remove($music);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Set the status of the given music to false
     * 
     * @param EntityManagerInterface $entityManager
     * @param Music $music
     * @return JsonResponse
     */
    #[Route('/api/music/{idMusic}', name: 'music.deleteStatus', methods: ['DELETE'])]
    #[ParamConverter("music", options : ["id" => "idMusic"])]
    public function deleteMusic(EntityManagerInterface $entityManager, Music $music): JsonResponse
    {
        $music->setStatus(false);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK, [], false);
    }

    /**
     * Set the status of the given music to true
     * 
     * @param EntityManagerInterface $entityManager
     * @param Music $music
     * @return JsonResponse
     */
    #[Route('/api/music/{idMusic}', name: 'music.turnOnStatus', methods: ['POST'])]
    #[ParamConverter("music", options : ["id" => "idMusic"])]
    public function turnonMusic(EntityManagerInterface $entityManager, Music $music): JsonResponse
    {
        $music->setStatus(true);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK, [], false);
    }

    /**
     * Create a music that will be link to an author 
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/music', name: 'music.create', methods: ['POST'])]
    public function createMusic(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $music = $serializer->deserialize(
            $request->getContent(),
            Music::class,
            "json"
        );
        $music->setStatus(true);

        // $content = $request->toArray();
        // rajouter le repo de l'entité concerné
        // $trucatrouver = $repoEnquestion->find($content['idDutruc'] ?? -1);
        // $music->setAuthor($trucatrouver);

        $error = $validator->validate($music);
        if($error->count() > 0) {
            return new JsonResponse($serializer->serialize($error, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($music);
        $entityManager->flush();

        $jsonMusic = $serializer->serialize($music, "json");
        $location = $urlGenerator->generate('music$music.get', ['idMusic' => $music->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonMusic, Response::HTTP_CREATED, ["location" => $location], true);
    }
}
