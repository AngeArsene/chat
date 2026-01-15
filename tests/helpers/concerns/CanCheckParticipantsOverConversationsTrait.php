<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Tests\Concerns;

use AngeArsene\Chat\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use AngeArsene\Chat\Contracts\ConversationParticipantInterface;

/**
 * 
 */
trait CanCheckParticipantsOverConversationsTrait
{
    private function checkParticipantsOverConversations(
        array | Collection | ConversationParticipantInterface $participants,
        array | Collection | Conversation $conversations
    ): void {
        $participants = (is_array($participants) || $participants instanceof Collection)
            ? $participants
            : [$participants];

        $conversations = (is_array($conversations) || $conversations instanceof Collection)
            ? $conversations
            : [$conversations];

        $this->assertDatabaseCount('participations', count($participants) * count($conversations));

        $count = 1;

        foreach ($conversations as $conversation) {
            foreach ($participants as $participant) {
                $this->assertDatabaseHas('participations', [
                    'id' => $count,
                    'conversation_id' => $conversation->id,
                    'messageable_id' => $participant->getKey(),
                    'messageable_type' => get_class($participant)
                ]);

                $count++;
            }
        }
    }
}
