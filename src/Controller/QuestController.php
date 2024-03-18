<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Quest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for managing quests.
 */
class QuestController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * QuestController constructor.
     * @param EntityManagerInterface $entityManager The entity manager to use for database operations.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Renders the index page for quests.
     *
     * @return Response
     *
     * @Route("/quest", name="app_quest")
     */
    #[Route('/quest', name: 'app_quest')]
    public function index(): Response
    {
        return $this->render('quest/quest_list.html.twig', [
            'controller_name' => 'QuestController',
        ]);
    }


    /**
     * API endpoint for creating a new quest.
     *
     * @param Request $request The HTTP request object.
     *
     * @return JsonResponse
     *
     * @Route("/api/quest/create", name="api_create_quest", methods={"POST"})
     */
    #[Route('/api/quest/create', name: 'api_create_quest', methods: ['POST'])]
    public function createQuest(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['price'])) {
            return new JsonResponse(['message' => 'Missing required parameters'], Response::HTTP_BAD_REQUEST);
        }

        $quest = new Quest();
        $quest->setName($data['name']);
        $quest->setPrice($data['price']);


        $this->entityManager->persist($quest);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Quest created successfully'], Response::HTTP_CREATED);
    }


}
