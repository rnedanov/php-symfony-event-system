<?php

namespace App\Controller;

use App\Entity\SubscriptionType;
use App\Entity\User;
use App\Entity\UserSubscription;
use App\Service\NotificationService;
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
    #[IsGranted('ROLE_USER')]
    public function save(
        Request $request,
        EntityManagerInterface $em,
        Security $security,
        NotificationService $notificationService
    ): Response {
        $user = $security->getUser();
        $subscriptionTypeIds = $request->request->all()['subscriptions'] ?? [];

        $currentSubscriptionIds = array_map(
            fn($us) => $us->getSubscriptionType()->getId(),
            $user->getUserSubscriptions()->toArray()
        );

        $newSubscriptionIds = array_diff($subscriptionTypeIds, $currentSubscriptionIds);

        foreach ($user->getUserSubscriptions() as $userSubscription) {
            $em->remove($userSubscription);
        }

        foreach ($subscriptionTypeIds as $typeId) {
            $subscriptionType = $em->getRepository(SubscriptionType::class)->find($typeId);

            if ($subscriptionType) {
                $userSubscription = new UserSubscription();
                $userSubscription->setUser($user);
                $userSubscription->setSubscriptionType($subscriptionType);
                $userSubscription->setSubscribedAt(new \DateTime());
                $em->persist($userSubscription);

                if (in_array($typeId, $newSubscriptionIds)) {
                    $notificationService->sendSubscriptionNotification($user, $subscriptionType);
                }
            }
        }

        $em->flush();

        return $this->redirectToRoute('subscriptions');
    }

    #[Route('/unsubscribe/all/{email}', name: 'unsubscribe_all')]
    #[IsGranted('ROLE_USER')]
    public function unsubscribeAll(string $email, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            foreach ($user->getUserSubscriptions() as $subscription) {
                $em->remove($subscription);
            }

            $em->flush();

            return $this->render('emails/unsubscribe_success.html.twig', [
                'message' => 'Вы отписались от всех уведомлений'
            ]);
        }

        return $this->render('emails/unsubscribe_error.html.twig');
    }
}
