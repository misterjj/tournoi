<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class RegisterController
{
    /**
     * @Route("/register", name="register")
     *
     * @param Environment $twig
     * @param FormFactory $formFactory
     * @return Response
     */
    public function index(Environment $twig, FormFactory $formFactory)
    {
        $participant = new Participant();
        $form = $formFactory->create(ParticipantType::class, $participant);

        $subscriptionDateStart = new DateTime(getenv('SUBSCRIPTION_DATE_START'));
        return new Response(
            $twig->render('register/index.html.twig', [
                'form' => $form->createView(),
                'subscriptionDateStart' => $subscriptionDateStart
            ])
        );
    }

    /**
     * @Route("/register-save", name="register_save")
     *
     * @param Environment $twig
     * @param Request $request
     * @param FormFactory $formFactory
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function save(Environment $twig, Request $request, FormFactory $formFactory, EntityManagerInterface $entityManager)
    {
        $subscriptionDateStart = new DateTime(getenv('SUBSCRIPTION_DATE_START'));
        $participant = new Participant();
        $form = $formFactory->create(ParticipantType::class, $participant);
        if ($subscriptionDateStart->getTimestamp() <= (new DateTime())->getTimestamp()) {

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $participant->setAddedDate(new \DateTime());
                $entityManager->persist($participant);
                $entityManager->flush();

                return new Response(
                    $twig->render('register/confirm.html.twig')
                );
            }
        }
        return new Response(
            $twig->render('register/index.html.twig', [
                'form' => $form->createView(),
                'subscriptionDateStart' => $subscriptionDateStart
            ])
        );
    }
}
