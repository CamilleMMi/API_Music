<?php

namespace App\Controller;

use DateTime;
use App\Entity\Albums;
use Doctrine\ORM\EntityManager;
use App\Repository\LikeRepository;
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

class LikeController extends AbstractController
{
    #[Route('/like', name: 'app_like')]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/LikeController.php',
        ]);
    }

    /**
     * Return all the albums existing in the data base
     *
     * @param LikeRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/albums', name: 'albums.getAll', methods: ["GET"])]
    // #[IsGranted('ADMIN')]
    public function getAllAlbums(Request $request, LikeRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $likes = $repository->findWithPagination($page, $limit);
        $context = SerializationContext::create()->setGroups(['getAllLike']);
        $jsonLikes = $serializer->serialize($likes, 'json', $context);
        return new JsonResponse($jsonLikes, Response::HTTP_OK, [], true);
    }

}
