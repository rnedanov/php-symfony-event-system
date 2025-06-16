<?php

namespace App\Controller;

use App\Entity\SubscriptionType;
use App\Form\SubscriptionTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/subscription-type')]
#[IsGranted('ROLE_ADMIN')]
class SubscriptionAdminController extends AbstractController
{
    #[Route('/new', name: 'subscription_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $subscriptionType = new SubscriptionType();
        $form = $this->createForm(SubscriptionTypeForm::class, $subscriptionType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($subscriptionType);
            $em->flush();

            return $this->redirectToRoute('subscriptions');
        }

        return $this->render('subscriptions/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}