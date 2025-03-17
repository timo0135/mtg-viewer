<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Artist;
use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/card', name: 'api_card_')]
#[OA\Tag(name: 'Card', description: 'Routes for all about cards')]
class ApiCardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/all', name: 'List all cards', methods: ['GET'])]
    #[OA\Parameter(name: 'page', description: 'Page number', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'limit', description: 'Limit of cards per page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Put(description: 'Return all cards in the database')]
    #[OA\Response(response: 200, description: 'List all cards')]
    public function cardAll(Request $request): Response
    {
        $this->logger->info('Starting to list all cards');
        $startTime = microtime(true);

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 100);
        $offset = ($page - 1) * $limit;

        try {
            $cards = $this->entityManager->getRepository(Card::class)->findBy([], null, $limit, $offset);
            $totalCards = $this->entityManager->getRepository(Card::class)->count([]);
            $this->logger->info('Successfully listed all cards');
        } catch (\Exception $e) {
            $this->logger->error('Error listing all cards: ' . $e->getMessage());
            return $this->json(['error' => 'An error occurred while listing cards'], 500);
        }

        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        $this->logger->info('Finished listing all cards', ['duration' => $duration]);

        return $this->json([
            'cards' => $cards,
            'total' => $totalCards,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    #[Route('/{uuid}', name: 'Show card', methods: ['GET'])]
    #[OA\Parameter(name: 'uuid', description: 'UUID of the card', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Put(description: 'Get a card by UUID')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardShow(string $uuid): Response
    {
        $this->logger->info('Starting to show card', ['uuid' => $uuid]);
        $startTime = microtime(true);

        try {
            $card = $this->entityManager->getRepository(Card::class)->findOneBy(['uuid' => $uuid]);
            if (!$card) {
                $this->logger->warning('Card not found', ['uuid' => $uuid]);
                return $this->json(['error' => 'Card not found'], 404);
            }
            $this->logger->info('Successfully found card', ['uuid' => $uuid]);
        } catch (\Exception $e) {
            $this->logger->error('Error showing card: ' . $e->getMessage(), ['uuid' => $uuid]);
            return $this->json(['error' => 'An error occurred while showing the card'], 500);
        }

        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        $this->logger->info('Finished showing card', ['uuid' => $uuid, 'duration' => $duration]);

        return $this->json($card);
    }
}
