<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/subscriptions', name: 'subscriptions')]
    public function subs(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $userData = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getName(),
                'email' => $user->getEmail(),
            ];
        }, $users);
        return $this->json($userData);
    }
}
