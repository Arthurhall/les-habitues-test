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

    public function getShops()
    {
        $response = $this->client->getShops();
    }
}
