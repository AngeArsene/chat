<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Tests\Feature\Conversation;

use AngeArsene\Chat\Facades\Chat;
use AngeArsene\Chat\Tests\TestCase;
use AngeArsene\Chat\Chat as ChatChat;
use AngeArsene\Chat\Tests\Models\User;
use AngeArsene\Chat\Models\Conversation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use AngeArsene\Chat\Services\ConversationService;
use AngeArsene\Chat\Providers\ChatServiceProvider;
use AngeArsene\Chat\Tests\Concerns\CanCheckParticipantsOverConversationsTrait;

#[CoversClass(Chat::class)]
#[CoversClass(ChatChat::class)]
#[CoversClass(Conversation::class)]
#[CoversClass(ChatServiceProvider::class)]
#[CoversMethod(ConversationService::class, 'set')]
#[CoversMethod(ConversationService::class, 'add')]
#[CoversMethod(ConversationService::class, 'create')]
#[CoversMethod(ConversationService::class, '__construct')]
final class AddFeatureTest extends TestCase
{
    use CanCheckParticipantsOverConversationsTrait;
    
    public function test_chat_adds_model_of_conversations_participant_after_conversation_creation(): void
    {
        $participant = User::factory()->create();

        $conversation1 = Chat::createConversation();
        $conversation2 = Chat::conversations()->create();

        Chat::conversations()->set($conversation1)->add($participant);
        Chat::conversations()->set($conversation2)->add($participant);

        $this->checkParticipantsOverConversations(
            $participant, [$conversation1, $conversation2]
        );
        $this->assertCount(count([$participant]), $conversation1->participants);
        $this->assertCount(count([$participant]), $conversation2->participants);
    }
}