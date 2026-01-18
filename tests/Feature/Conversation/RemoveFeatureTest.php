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

    public function test_chat_can_remove_single_participant_to_conversations(): void
    {
        $participant = $this->createParticipants();

        $conversations = $this->createConversations($participant);

        Chat::conversations()->set($conversations[1])->remove($participant);
        Chat::conversations()->set($conversations[2])->remove($participant);

        $this->assertCount(0, $conversations[1]->participants);
        $this->assertCount(0, $conversations[2]->participants);
    }
}
