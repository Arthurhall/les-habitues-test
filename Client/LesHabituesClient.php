<?php

namespace App\Client;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * API test LesHabitues
 *
 * @author arthu
 */
class LesHabituesClient extends Client
{
    /**
     * @param int $page
     * @param string $search
     *
     * @return ResponseInterface
     */
    public function getShops(int $page = 1) : ResponseInterface
    {
        return $this->get(sprintf('shops/?page=%d', $page));
    }
}
