<?php

namespace App\Manager;

use App\Client\LesHabituesClient;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Manage client API "les habitués".
 *
 * @see LesHabituesClient
 *
 * @author arthu
 */
class LesHabituesApiManager
{
    /**
     * @var LesHabituesClient
     */
    private $client;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param LesHabituesClient $client
     * @param SerializerInterface $serializer
     */
    public function __construct(LesHabituesClient $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function getLocalisations(int $page = 1): array
    {
        $response = $this->client->getShops($page);
        $content = $this->serializer->decode($response->getBody()->getContents(), 'json');

        $shops = [];
        foreach ($content['data'] as $chain) {
            foreach ($chain['localisations'] as $localisation) {
                $shops[] = $localisation;
            }
        }

        return $shops;
    }
}
