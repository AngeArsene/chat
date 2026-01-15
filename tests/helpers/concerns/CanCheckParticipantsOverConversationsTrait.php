<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Tests\Concerns;

use AngeArsene\Chat\Facades\Chat;
use AngeArsene\Chat\Tests\Models\Book;
use AngeArsene\Chat\Tests\Models\User;
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

    private function createParticipants(?bool $isValid = true, ?int $count = 1, ?bool $arr = false): mixed
    {
        $participants = [];

        if (!$arr) {
            $participants = $isValid
                ? User::factory($count)->create()
                : Book::factory($count)->create();
        } else {
            for ($i = 1; $i <= $count; $i++) {
                $participants[] = $isValid
                    ? User::factory()->create()
                    : Book::factory()->create();
            }
        }

        return count($participants) === 1 ? $participants[0] : $participants;
    }

    private function createConversations(
        null |
        array |
        Collection |
        ConversationParticipantInterface $participants = null
    ): array {
        $conversation1 = Chat::conversations()->create($participants);
        $conversation2 = Chat::createConversation($participants);

        return [1 => $conversation1, $conversation2];
    }
}
