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
#[CoversMethod(ConversationService::class, '__construct')]
#[CoversTrait(NormalizesParticipantsOrConversations::class)]
final class AddFeatureTest extends TestCase
{
    use ManagesParticipantAssertions;
    
    public function test_chat_can_add_single_participant_to_conversation_after_it_was_created(): void
    {
        $participant = $this->createParticipants();

        $conversations = $this->createConversations();

        Chat::conversations()->set($conversations[1])->add($participant);
        Chat::conversations()->set($conversations[2])->add($participant);

        $this->assertParticipantsInConversations($participant, $conversations);
    }
    
    public function test_chat_cannot_add_single_non_participant_to_conversation_after_it_was_created(): void
    {
        $participant = $this->createParticipants(isValid: false);

        $conversations = $this->createConversations();

        $this->expectException(\TypeError::class);

        Chat::conversations()->set($conversations[1])->add($participant);
        Chat::conversations()->set($conversations[2])->add($participant);
    }
    
    public function test_chat_can_add_array_participants_to_conversation_after_it_was_created(): void
    {
        $participants = $this->createParticipants(count: 2, arr: true);

        $conversations = $this->createConversations();

        Chat::conversations()->set($conversations[1])->add($participants);
        Chat::conversations()->set($conversations[2])->add($participants);

        $this->assertParticipantsInConversations($participants, $conversations);
    }
    
    public function test_chat_cannot_add_array_non_participants_to_conversation_after_it_was_created(): void
    {
        $participants = $this->createParticipants(isValid: false, count: 2, arr: true);

        $conversations = $this->createConversations();

        $this->expectException(\InvalidArgumentException::class);

        Chat::conversations()->set($conversations[1])->add($participants);
        Chat::conversations()->set($conversations[2])->add($participants);
    }
    
    public function test_chat_can_add_collection_participants_to_conversation_after_it_was_created(): void
    {
        $participants = $this->createParticipants(count: 2, arr: false);

        $conversations = $this->createConversations();

        Chat::conversations()->set($conversations[1])->add($participants);
        Chat::conversations()->set($conversations[2])->add($participants);

        $this->assertParticipantsInConversations($participants, $conversations);
    }
    
    public function test_chat_cannot_add_collection_non_participants_to_conversation_after_it_was_created(): void
    {
        $participants = $this->createParticipants(isValid: false, count: 2, arr: false);

        $conversations = $this->createConversations();

        $this->expectException(\InvalidArgumentException::class);

        Chat::conversations()->set($conversations[1])->add($participants);
        Chat::conversations()->set($conversations[2])->add($participants);
    }
}