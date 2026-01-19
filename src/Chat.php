<?php

declare(strict_types=1);

namespace AngeArsene\Chat;

use AngeArsene\Chat\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use AngeArsene\Chat\Services\ConversationService;
use AngeArsene\Chat\Contracts\ConversationParticipantInterface;

final class Chat
{
    public function __construct(
        private ConversationService $conversationService
    ) {}

    public function createConversation(
        null |
        array |
        Collection |
        ConversationParticipantInterface $participants = null
    ): Conversation {
        return $this->conversations()->create($participants);
    }

    public function conversations(): ConversationService
    {
        return $this->conversationService;
    }
}
