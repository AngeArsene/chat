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
    
    public function test_chat_can_add_single_participant_to_conversation_after_it_was_created(): void
    {
        $participant = $this->createParticipants();

        $conversations = $this->createConversations();

        Chat::conversations()->set($conversations[1])->add($participant);
        Chat::conversations()->set($conversations[2])->add($participant);

        $this->checkParticipantsOverConversations($participant, $conversations);
        $this->assertCount(count([$participant]), $conversations[1]->participants);
        $this->assertCount(count([$participant]), $conversations[2]->participants);
    }
    
    public function test_chat_can_add_array_participants_to_conversation_after_it_was_created(): void
    {
        $participants = $this->createParticipants(count: 2, arr: true);

        $conversations = $this->createConversations();

        Chat::conversations()->set($conversations[1])->add($participants);
        Chat::conversations()->set($conversations[2])->add($participants);

        $this->checkParticipantsOverConversations($participants, $conversations);
        $this->assertCount(count($participants), $conversations[1]->participants);
        $this->assertCount(count($participants), $conversations[2]->participants);
    }
    
    public function test_chat_can_add_collection_participants_to_conversation_after_it_was_created(): void
    {
        $participants = $this->createParticipants(count: 2, arr: false);

        $conversations = $this->createConversations();

        Chat::conversations()->set($conversations[1])->add($participants);
        Chat::conversations()->set($conversations[2])->add($participants);

        $this->checkParticipantsOverConversations($participants, $conversations);
        $this->assertCount(count($participants), $conversations[1]->participants);
        $this->assertCount(count($participants), $conversations[2]->participants);
    }
}