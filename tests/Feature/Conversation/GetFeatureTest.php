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
#[CoversMethod(ConversationService::class, 'get')]
#[CoversMethod(ConversationService::class, '__construct')]
#[CoversTrait(NormalizesParticipantsOrConversations::class)]
final class GetFeatureTest extends TestCase
{
    use ManagesParticipantAssertions;

    public function test_chat_can_get_conversations_by_id(): void
    {
        $conv = $this->createConversations();

        $conversation1 = Chat::conversations()->get($conv[1]->id);
        $conversation2 = Chat::conversations()->get($conv[2]->id);

        $this->assertEquals($conv[1]->id, $conversation1->id);
        $this->assertEquals($conv[2]->id, $conversation2->id);
    }

    public function test_chat_can_get_conversations_by_conversation_model_instance(): void
    {
        $conv = $this->createConversations();

        $conversation1 = Chat::conversations()->get($conv[1]);
        $conversation2 = Chat::conversations()->get($conv[2]);

        $this->assertEquals($conv[1]->id, $conversation1->id);
        $this->assertEquals($conv[2]->id, $conversation2->id);
    }
}