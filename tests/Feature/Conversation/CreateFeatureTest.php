<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Tests\Feature\Conversation;

use AngeArsene\Chat\Facades\Chat;
use AngeArsene\Chat\Tests\TestCase;
use AngeArsene\Chat\Chat as ChatChat;
use AngeArsene\Chat\Tests\Models\Book;
use AngeArsene\Chat\Tests\Models\User;
use AngeArsene\Chat\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\CoversMethod;
use AngeArsene\Chat\Services\ConversationService;
use AngeArsene\Chat\Providers\ChatServiceProvider;
use AngeArsene\Chat\Contracts\ConversationParticipantInterface;
use AngeArsene\Chat\Tests\Concerns\CanCheckParticipantsOverConversationsTrait;

#[CoversClass(Chat::class)]
#[CoversClass(ChatChat::class)]
#[CoversClass(Conversation::class)]
#[CoversClass(ChatServiceProvider::class)]
#[CoversMethod(ConversationService::class, 'set')]
#[CoversMethod(ConversationService::class, 'add')]
#[CoversMethod(ConversationService::class, 'create')]
#[CoversMethod(ConversationService::class, '__construct')]
final class CreateFeatureTest extends TestCase
{
    use CanCheckParticipantsOverConversationsTrait;
    
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

    public static function convProvider(): array
    {
        return [
            [
                ['id' => 1], ['id' => 2]
            ]
        ];
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

    #[DataProvider('convProvider')]
    public function test_chat_can_create_conversation_with_no_participants(array $conv1, array $conv2): void
    {
        $conversations = $this->createConversations();

        $this->assertDatabaseCount('conversations', count($conversations))
            ->assertDatabaseHas('conversations', $conv1)
            ->assertDatabaseHas('conversations', $conv2);

        $this->assertEquals($conversations[1]->id, $conv1['id']);
        $this->assertEquals($conversations[2]->id, $conv2['id']);

        $this->assertCount(0, $conversations[1]->participants);
        $this->assertCount(0, $conversations[2]->participants);
    }

    public function test_chat_creates_conversations_with_single_valid_participant(): void
    {
        /** @var ConversationParticipantInterface */
        $participant = $this->createParticipants();

        $conversations = $this->createConversations($participant);

        $this->checkParticipantsOverConversations($participant, $conversations);

        $this->assertCount(count([$participant]), $conversations[1]->participants);
        $this->assertCount(count([$participant]), $conversations[2]->participants);
    }

    public function test_chat_cannot_create_conversations_with_single_invalid_participant(): void
    {
        $participant = $this->createParticipants(isValid: false);

        $this->expectException(\TypeError::class);

        $this->createConversations($participant);
    }

    public function test_chat_creates_conversations_with_array_valid_participants(): void
    {
        $participants = $this->createParticipants(count: 2, arr: true);

        $conversations = $this->createConversations($participants);

        $this->checkParticipantsOverConversations($participants, $conversations);

        $this->assertCount(count($participants), $conversations[1]->participants);
        $this->assertCount(count($participants), $conversations[2]->participants);
    }

    public function test_chat_cannot_create_conversations_with_array_invalid_participants(): void
    {
        $participant = $this->createParticipants(isValid: false, count: 2, arr: true);

        $this->expectException(\InvalidArgumentException::class);

        $this->createConversations($participant);
    }

    public function test_chat_creates_conversations_with_collection_valid_participants(): void
    {
        $participants = $this->createParticipants(count: 2);

        $conversations = $this->createConversations($participants);

        $this->checkParticipantsOverConversations($participants, $conversations);

        $this->assertCount(count($participants), $conversations[1]->participants);
        $this->assertCount(count($participants), $conversations[2]->participants);
    }

    public function test_chat_cannot_create_conversations_with_collection_invalid_participants(): void
    {
        $participant = $this->createParticipants(isValid: false, count: 2, arr: false);

        $this->expectException(\InvalidArgumentException::class);

        $this->createConversations($participant);
    }
}
