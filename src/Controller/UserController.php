<?php

namespace App\Controller;

use App\Entity\CompletedQuest;
use App\Entity\User;
use App\Entity\Quest;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class UserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * UserController constructor.
     * @param EntityManagerInterface $entityManager The entity manager interface
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Creates a new user.
     *
     * @param Request $request The HTTP request
     * @return JsonResponse The JSON response
     *
     * @Route('/api/user/create', name: 'api_create_user', methods: ['POST'])
     */
    #[Route('/api/user/create', name: 'api_create_user', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['balance'])) {
            return new JsonResponse(['message' => 'Missing required parameters'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setName($data['name']);
        $user->setBalance($data['balance']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User created successfully'], Response::HTTP_CREATED);
    }

    /**
     * Retrieves a list of users.
     *
     * @return Response The HTTP response
     *
     * @Route('/users', name: 'app_users')
     */
    #[Route('/users', name: 'app_users')]
    public function userList(): Response
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        return $this->render('user/user_list.html.twig', [
            'users' => $users,
        ]);
    }


    /**
     * Displays the profile of a specific user.
     *
     * @param User $user The user entity
     * @return Response The HTTP response
     *
     * @Route('/users/{id}', name: 'app_user_profile')
     */
    #[Route('/users/{id}', name: 'app_user_profile')]
    public function userProfile(User $user): Response
    {

        $allQuests = $this->entityManager->getRepository(Quest::class)->findAll();

        $questArray = $user->getQuests()->toArray();
        $availableQuests = array_filter($allQuests, function ($quest) use ($questArray) {
            return !in_array($quest, $questArray);
        });


        return $this->render('user/user_profile.html.twig', [
            'user' => $user,
            'availableQuests' => $availableQuests,
        ]);
    }

    /**
     * Marks a quest as completed by a user.
     *
     * @param Request $request The HTTP request
     * @return Response The HTTP response
     *
     * @Route('/api/quest/complete', name: 'api_complete_quest', methods: ['POST'])
     */
    #[Route('/api/quest/complete', name: 'api_complete_quest', methods: ['POST'])]
    public function completeQuest(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['user_id']) || !isset($data['quest_id'])) {
            return new Response('Missing required parameters', Response::HTTP_BAD_REQUEST);
        }

        $user = $this->entityManager->getRepository(User::class)->find($data['user_id']);
        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $quest = $this->entityManager->getRepository(Quest::class)->find($data['quest_id']);
        if (!$quest) {
            return new Response('Quest not found', Response::HTTP_NOT_FOUND);
        }

        $questCost = $quest->getPrice();
        $userBalance = $user->getBalance();
        $user->addQuest($quest);
        $newBalance = $userBalance + $questCost;
        $user->setBalance($newBalance);
        $completedQuest = new CompletedQuest();
        $completedQuest->setUser($user);
        $completedQuest->setQuest($quest);
        $completedQuest->setCompletedAt(new DateTime());

        $this->entityManager->persist($completedQuest);
        $this->entityManager->flush();

        return new Response('Quest completed successfully', Response::HTTP_OK);
    }

    /**
     * Retrieves the completed quests of a specific user.
     *
     * @param int $userId The user ID
     * @return JsonResponse The JSON response
     *
     * @Route('/api/completed-quests/{userId}', name: 'api_completed_quests', methods: ['GET'])
     */
    #[Route('/api/completed-quests/{userId}', name: 'api_completed_quests', methods: ['GET'])]
    public function getCompletedQuests(int $userId): JsonResponse
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $completedQuests = $user->getCompletedQuests();

        $responseData = [];
        foreach ($completedQuests as $completedQuest) {
            $responseData[] = [
                'quest_name' => $completedQuest->getQuest()->getName(),
                'completion_time' => $completedQuest->getCompletedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($responseData, Response::HTTP_OK);
    }


    /**
     * Marks a quest as completed by a user.
     *
     * This method receives a POST request containing the IDs of the user and quest.
     * It retrieves the corresponding user and quest entities from the database,
     * updates the user's balance based on the quest's price, adds the quest to the user's completed quests,
     * creates a new CompletedQuest entity, persists it to the database, and redirects to the user's profile page.
     *
     * @param Request $request The HTTP request object containing user and quest IDs
     * @return Response A response indicating the success of the operation
     */
    public function markQuestCompleted(Request $request): Response
    {
        $userId = $request->get('userId');
        $questId = $request->get('questId');

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $quest = $this->entityManager->getRepository(Quest::class)->find($questId);

        if (!$user) {
            throw $this->createNotFoundException('Пользователь не найден');
        }

        $questCost = $quest->getPrice();
        $userBalance = $user->getBalance();
        $user->addQuest($quest);

        $newBalance = $userBalance + $questCost;
        $user->setBalance($newBalance);

        $completedQuest = new CompletedQuest();
        $completedQuest->setUser($user);
        $completedQuest->setQuest($quest);
        $completedQuest->setCompletedAt(new DateTime());

        $this->entityManager->persist($completedQuest);
        $this->entityManager->flush();

        $this->addFlash('success', 'Задание выполнено успешно!');
        return $this->redirectToRoute('app_user_profile', ['id' => $userId]);
    }
}
