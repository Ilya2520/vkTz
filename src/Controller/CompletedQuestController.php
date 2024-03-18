<?php

namespace App\Controller;

use App\Entity\CompletedQuest;
use App\Entity\Quest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for managing completed quests.
 */
class CompletedQuestController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @Route("/", name="quest_list")
     */
    #[Route('/quests', name: 'app_quests')]
    public function userList(): Response
    {
        $userRepository = $this->entityManager->getRepository(Quest::class);
        $quests = $userRepository->findAll();

        return $this->render('quest/quest_list.html.twig', [
            'quests' => $quests,
        ]);
    }

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager The entity manager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieves a list of completed quests.
     *
     * @return JsonResponse JSON response containing the list of completed quests
     *
     * @Route('/api/completed-quests/', name: 'completed_quests', methods: ['GET'])
     */
    #[Route('/api/completed-quests/', name: 'completed_quests', methods: ['GET'])]
    public function getCompletedQuests(): JsonResponse
    {
        $repository = $this->entityManager->getRepository(CompletedQuest::class);

        $completedQuests = $repository->findAll();

        $responseData = [];
        foreach ($completedQuests as $completedQuest) {
            $user = $completedQuest->getUser();
            $quest = $completedQuest->getQuest();
            $responseData[] = [
                'user' => ['id' => $user->getId(), 'name' => $user->getName()],
                'quest' => ['id' => $quest->getId(), 'name' => $quest->getName(), 'price' => $quest->getPrice()],
                'completion_time' => $completedQuest->getCompletedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($responseData, Response::HTTP_OK);
    }
}
