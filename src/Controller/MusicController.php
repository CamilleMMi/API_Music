<?php

namespace App\Controller;

use App\Entity\Music;
use App\Repository\MusicRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
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
    public function getAllMusic(MusicRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $music = $repository->findAll();
        $jsonMusic = $serializer->serialize($music, 'json', ["groups" => "getAllMusics"]);

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
        $jsonMusic = $serializer->serialize($music, 'json', ["groups" => "getMusic"]);

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
}
