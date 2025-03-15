<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/messages')]
final class MessengerController extends AbstractController
{
    #[Route('/chats', name: 'chat_list', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function chatList(MessageRepository $messageRepo): Response
    {
        $user = $this->getUser();
        $chatUsers = $messageRepo->findChatUsers($user);

        return $this->render('messenger/list.html.twig', [
            'chatUsers' => $chatUsers,
        ]);
    }

    #[Route('/{userId<\d+>}', name: 'get_messages', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function getMessages(int $userId, MessageRepository $messageRepo, UserRepository $userRepo): Response
    {
        $user = $this->getUser();
        $otherUser = $userRepo->find($userId);

        if (!$otherUser) {
            throw $this->createNotFoundException('User not found.');
        }

        $messages = $messageRepo->findMessagesBetweenUsers($user, $otherUser);

        return $this->render('messenger/messages.html.twig', [
            'messages' => $messages,
            'currentUser' => $user,
            'otherUser' => $otherUser,
        ]);
    }

    #[Route('/send', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $sender = $this->getUser();
        $receiverId = $request->request->get('receiver_id');
        $text = $request->request->get('text');

        $receiver = $userRepo->find($receiverId);

        if (!$receiver) {
            throw $this->createNotFoundException('Receiver not found.');
        }

        $message = new Message();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setText($text);
        $message->setDateMessage(new \DateTime()); 
        $message->setTimeMessage(new \DateTime()); 

        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute('get_messages', ['userId' => $receiverId]);
    }
}
