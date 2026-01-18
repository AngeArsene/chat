<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Contracts;

interface ConversationParticipantInterface
{
    /**
     * Get the unique identifier for the participant.
     *
     * @return mixed
     */
    public function getKey();
}