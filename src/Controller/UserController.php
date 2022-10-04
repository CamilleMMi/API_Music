<?php

namespace App\Controller;

use Serializable;
use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Bundle\FrameworkBundle\Abstract


class UserController extends AbstractController
{
    #[Route('/api/user', name:'user.all', methods: ['GET'])]
    public function getAllUser(UserRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $user = $repository->findAll();
        $jsonUser = $serializer->serialize($user, 'json', ["groups" => "getAllUser"]);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    // /**
    //  * Get User by Id route
    //  * 
    //  * @param int $id
    //  * @param UserRepository $repository
    //  * @param SerializerInterface $serializer
    //  * @return JsonResponse
    //  */
    // #[Route('/api/user/{idUser}', name: 'user.get', methods: ['GET'])]
    // public function getOneUser(int $id, UserRepository $repository, SerializerInterface $serializer): JsonResponse
    // {
    //     $user = $repository->find($id);
    //     $jsonUser = $serializer->serialize($user, 'json');
    //     return $user ?
    //     new JsonResponse($jsonUser, Response::HTTP_OK, [], true) :
    //     new JsonResponse($jsonUser, Response::HTTP_NOT_FOUND);
    // }

    #[Route('/api/user/{idUser}', name: 'user.get', methods: ['GET'])]
    #[ParamConverter("user", options : ["id"=> "idUser"])]
    public function getOneUser(User $user, SerializerInterface $serializer): JsonResponse
    {
        $jsonUser = $serializer->serialize($user, 'json', ["groups" => "getUser"]);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }
}

