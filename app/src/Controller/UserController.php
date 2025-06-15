<?php

namespace App\Controller;

use App\Entity\SubscriptionType;
use App\Entity\UserSubscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[Route('/subscriptions', name: 'subscriptions')]
    #[IsGranted('ROLE_USER')]
    public function subs(EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        $allSubscriptionTypes = $em->getRepository(SubscriptionType::class)->findAll();
        
        $userSubscriptionTypes = array_map(
            fn(UserSubscription $us) => $us->getSubscriptionType(),
            $user->getUserSubscriptions()->toArray()
        );

        return $this->render('subscriptions/index.html.twig', [
            'allSubscriptionTypes' => $allSubscriptionTypes,
            'userSubscriptionTypes' => $userSubscriptionTypes,
        ]);
    }

    #[Route('/save-subscriptions', name: 'save_subscriptions', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        $subscriptionTypeIds = $request->request->all()['subscriptions'] ?? [];
    
        if (!is_array($subscriptionTypeIds)) {
            $subscriptionTypeIds = [$subscriptionTypeIds];
        }

        foreach ($user->getUserSubscriptions() as $userSubscription) {
            $em->remove($userSubscription);
        }
        
        // Добавляем новые подписки
        foreach ($subscriptionTypeIds as $typeId) {
            $subscriptionType = $em->getRepository(SubscriptionType::class)->find($typeId);
            
            if ($subscriptionType) {
                $userSubscription = new UserSubscription();
                $userSubscription->setUser($user);
                $userSubscription->setSubscriptionType($subscriptionType);
                
                $em->persist($userSubscription);
            }
        }
        
        $em->flush();
        
        $this->addFlash('success', 'Subscriptions updated successfully!');
        return $this->redirectToRoute('subscriptions');
    }
}
