<?php
// src/Controller/ChatController.php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'chat')]
    public function chat(Request $request, HubInterface $hub): Response
    {
        $form = $this->createFormBuilder()
            ->add('destinataire', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
            ->add('message', TextType::class, ['attr' => ['autocomplete' => 'off']])
            ->add('send', SubmitType::class)
            ->getForm();

        $emptyForm = clone $form; // Used to display an empty form after a POST request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // 🔥 The magic happens here! 🔥
            // The HTML update is pushed to the client using Mercure
            $hub->publish(new Update(
                'chat/'.$data['destinataire']->getId(),
                $this->renderView('chat/message.stream.html.twig', [
                    'message' => $data['message'],
                    'user' => $this->getUser()
                ]),
                false
            ));

            $hub->publish(new Update(
                'chatnb',
                $this->renderView('chat/chatnb.stream.html.twig', ['nb' => rand(1, 100)])
            ));

            // Force an empty form to be rendered below
            // It will replace the content of the Turbo Frame after a post
            $form = $emptyForm;
        }

        return $this->render('chat/index.html.twig', [
            'form' => $form,
        ]);
    }
}
