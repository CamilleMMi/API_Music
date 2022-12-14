<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PictureController extends AbstractController
{
    #[Route('/', name: 'app_picture')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PictureController.php',
        ]);
    }
    
    /**
     * Return all the pictures existing in the data base
     *
     * @param AlbumsRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/picutre$pictures', name: 'picutre$pictures.getAll', methods: ["GET"])]
    #[IsGranted('ROLE_ADMIN')]
    public function getAllAlbums(Request $request, PictureRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $pictures = $repository->findWithPagination($page, $limit);
        $jsonAlbums = $serializer->serialize($pictures, 'json', ['groups' => 'getAllAlbums']);
        return new JsonResponse($jsonAlbums, Response::HTTP_OK, [], true);
    }

    /**
     * Post the picture given in the request
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/picture', name: 'picture_create', methods: ['POST'])]
    public function createPicture(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $picture = new Picture();
        $files = $request->files->get("file");

        $picture->setFile($files)
        ->setMimeType($files->getClientMimeType())
        ->setRealName($files->getClientOriginalName())

        ->setPublicPath('/assets/pictures')
        ->setStatus(true)
        ->setUploadDate(new  \DateTime());

        $entityManager->persist($picture);
        $entityManager->flush();

        $jsonPicture = $serializer->serialize($picture, 'json', ["groups" => "getPicture"]);
        $location = $urlGenerator->generate('get_picture', ['idPicture' => $picture->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);

        return new JsonResponse($jsonPicture, Response::HTTP_CREATED, ['Location' => $location], "json");
    }

    /**
     * Return the picture referenced by the id given on the request
     * 
     * @param Picture $picture
     * @param SerializerInterface $serializer
     * @param Request $request
     */
    #[Route('/api/picture/{idPicture}', name: 'get_picture', methods: ['GET'])]
    #[ParamConverter("picture", options: ["id" => "idPicture"])]
    public function getPicture(Picture $picture, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $RLlocation = $picture->getPublicPath() . '/' . $picture->getRealPath();
        $location = $request->getUriForPath('/');
        //$location = $urlGenerator->generate('app_picture', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $location = $location . str_replace('/assets', 'assets', $RLlocation);

        return new JsonResponse($serializer->serialize($picture, 'json', ["groups" => "getPicture"]), Response::HTTP_OK, ['Location' => $location], true);
        // return new JsonResponse($location, Response::HTTP_OK, [], true);
    }

    /**
     * Remove entirely a picture depending on the id given
     *
     * @param Picture $picture
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    //#[Route('/api/picture/{idPicture}/remove', name: 'picture.deleteData', methods: ['DELETE'])]
    #[ParamConverter("picture", options : ["id" => "idPicture"])]
    public function deleteDataPicture(EntityManagerInterface $entityManager, Picture $picture, SerializerInterface $serializer): JsonResponse
    {
        $entityManager->remove($picture);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Set the status of the given picture to false
     * 
     * @param EntityManagerInterface $entityManager
     * @param Picture $picture
     * @return JsonResponse
     */
    #[Route('/api/picture/{idPicture}', name: 'picture.turnOffStatus', methods: ['DELETE'])]
    #[ParamConverter("picture", options : ["id" => "idPicture"])]
    public function turnoffPicture(EntityManagerInterface $entityManager, Picture $picture): JsonResponse
    {
        $picture->setStatus(false);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK, [], false);
    }

    /**
     * Set the status of the given picture to true
     * 
     * @param EntityManagerInterface $entityManager
     * @param Picture $picture
     * @return JsonResponse
     */
    #[Route('/api/picture/{idPicture}', name: 'picture.turnOnStatus', methods: ['POST'])]
    #[ParamConverter("picture", options : ["id" => "idPicture"])]
    public function turnonPicture(EntityManagerInterface $entityManager, Picture $picture): JsonResponse
    {
        $picture->setStatus(true);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_OK, [], false);
    }
}