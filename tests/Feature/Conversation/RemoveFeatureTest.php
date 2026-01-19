<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Tests\Feature\Conversation;

use AngeArsene\Chat\Facades\Chat;
use AngeArsene\Chat\Tests\TestCase;
use AngeArsene\Chat\Chat as ChatChat;
use AngeArsene\Chat\Models\Conversation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\CoversMethod;
use AngeArsene\Chat\Services\ConversationService;
use AngeArsene\Chat\Providers\ChatServiceProvider;
use AngeArsene\Chat\Tests\Concerns\ManagesParticipantAssertions;
use AngeArsene\Chat\Concerns\NormalizesParticipantsOrConversations;

#[CoversClass(Chat::class)]
#[CoversClass(ChatChat::class)]
#[CoversClass(Conversation::class)]
#[CoversClass(ChatServiceProvider::class)]
#[CoversMethod(ConversationService::class, 'set')]
#[CoversMethod(ConversationService::class, 'add')]
#[CoversMethod(ConversationService::class, 'create')]
#[CoversMethod(ConversationService::class, 'remove')]
#[CoversMethod(ConversationService::class, '__construct')]
#[CoversTrait(NormalizesParticipantsOrConversations::class)]
final class RemoveFeatureTest extends TestCase
{
    use ManagesParticipantAssertions;

    private function removeParticipantsFromConversations(mixed $participants, array $conversations): void
    {
        Chat::conversations()->set($conversations[1])->remove($participants);
        Chat::conversations()->set($conversations[2])->remove($participants);
    }

    private function assertAllConversationsParticipantsRemoved(array $conversations, mixed $participants): void
    {
        $this->assertCount(0, $conversations[1]->participants);
        $this->assertCount(0, $conversations[2]->participants);
        
        $this->assertDatabaseCount('participations', 0);

        $participants = $this->normalizeNotToNull($participants);

        foreach ($conversations as $conversation) {
            foreach ($participants as $participant) {
                $this->assertDatabaseMissing('participations', [
                    'conversation_id' => $conversation->id,
                    'messageable_id' => $participant->getKey(),
                    'messageable_type' => get_class($participant)
                ]);
            }
        }
    }

    public function test_chat_can_remove_single_participant_from_conversations(): void
    {
        $participant = $this->createParticipants();

        $conversations = $this->createConversations($participant);

        $this->removeParticipantsFromConversations($participant, $conversations);

        $this->assertAllConversationsParticipantsRemoved($conversations, $participant);
    }

    public function test_chat_cannot_remove_single_non_participant_from_conversations(): void
    {
        $participant = $this->createParticipants(isValid: false);

        $conversations = $this->createConversations($this->createParticipants());

        $this->expectException(\TypeError::class);

        $this->removeParticipantsFromConversations($participant, $conversations);
    }

    public function test_chat_can_remove_array_participants_from_conversations(): void
    {
        /** @var array */
        $participants = $this->createParticipants(count: 2, arr: true);

        $conversations = $this->createConversations($participants);

        $this->removeParticipantsFromConversations($participants, $conversations);

        $this->assertAllConversationsParticipantsRemoved($conversations, $participants);
    }

    public function test_chat_cannot_remove_array_non_participants_from_conversations(): void
    {
        $participants = $this->createParticipants(isValid: false, count: 2, arr: true);

        $conversations = $this->createConversations($this->createParticipants());

        $this->expectException(\InvalidArgumentException::class);

        $this->removeParticipantsFromConversations($participants, $conversations);
    }

    public function test_chat_can_remove_collection_participants_from_conversations(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection */
        $participants = $this->createParticipants(count: 2, arr: false);

        $conversations = $this->createConversations($participants);

        $this->removeParticipantsFromConversations($participants, $conversations);

        $this->assertAllConversationsParticipantsRemoved($conversations, $participants);
    }

    public function test_chat_cannot_remove_collection_non_participants_from_conversations(): void
    {
        $participants = $this->createParticipants(isValid: false, count: 2, arr: false);

        $conversations = $this->createConversations($this->createParticipants());

        $this->expectException(\InvalidArgumentException::class);

        $this->removeParticipantsFromConversations($participants, $conversations);
    }
}
