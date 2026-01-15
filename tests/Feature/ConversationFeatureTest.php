<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Tests\Feature;

use AngeArsene\Chat\Facades\Chat;
use AngeArsene\Chat\Tests\TestCase;
use AngeArsene\Chat\Tests\Models\Book;
use AngeArsene\Chat\Tests\Models\User;
use AngeArsene\Chat\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\CoversNamespace;
use AngeArsene\Chat\Contracts\ConversationParticipantInterface;

#[CoversNamespace('AngeArsene\\Chat')]
final class ConversationFeatureTest extends TestCase
{
    public function test_chat_creates_conversation_on_method_call_create_conversation(): void
    {
        $conv1 = ['id' => 1];
        $conv2 = ['id' => 2];

        $conversation1 = Chat::conversations()->create();
        $conversation2 = Chat::createConversation();

        $this->assertDatabaseCount('conversations', 2)
            ->assertDatabaseHas('conversations', $conv1)
            ->assertDatabaseHas('conversations', $conv2);

        $this->assertEquals($conversation1->id, $conv1['id']);
        $this->assertEquals($conversation2->id, $conv2['id']);
    }

    public function test_chat_conversations_returns_conversation_by_id(): void
    {
        $conv = Chat::conversations()->create();

        $conversation1 = Chat::conversations()->getById($conv);
        $conversation2 = Chat::conversations()->getById($conv->id);

        $this->assertEquals($conv->id, $conversation1->id);
        $this->assertEquals($conv->id, $conversation2->id);
    }

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

    public function test_chat_creates_conversations_with_participants_array(): void
    {
        $ange = User::factory()->create(['name' => 'Ange']);
        $arsene = User::factory()->create(['name' => 'Arsene']);

        $participants = [$ange, $arsene];

        $conversation1 = Chat::conversations()->create($participants);
        $conversation2 = Chat::createConversation($participants);

        $this->checkParticipantsOverConversations($participants, [$conversation1, $conversation2]);

        $this->assertCount(count($participants), $conversation1->participants);
        $this->assertCount(count($participants), $conversation2->participants);
    }

    public function test_chat_creates_conversations_with_participants_collection(): void
    {
        $participants = User::factory(2)->create();

        $conversation1 = Chat::conversations()->create($participants);
        $conversation2 = Chat::createConversation($participants);

        $this->checkParticipantsOverConversations($participants, [$conversation1, $conversation2]);

        $this->assertCount(count($participants), $conversation1->participants);
        $this->assertCount(count($participants), $conversation2->participants);
    }

    public function test_chat_creates_conversations_with_participant_model(): void
    {
        $ange = User::factory()->create(['name' => 'Ange']);

        $conversation1 = Chat::conversations()->create($ange);
        $conversation2 = Chat::createConversation($ange);

        $this->checkParticipantsOverConversations($ange, [$conversation1, $conversation2]);

        $this->assertCount(1, $conversation1->participants);
        $this->assertCount(1, $conversation2->participants);
    }

    public function test_chat_facade_throws_exception_when_conversation_created_with_non_participant_model(): void
    {
        $coding_book = Book::factory()->create(['name' => 'How To Code']);

        $this->expectException(\TypeError::class);

        Chat::conversations()->create($coding_book);
    }

    public function test_chat_throws_exception_when_conversation_created_with_non_participant_model(): void
    {
        $coding_book = Book::factory()->create(['name' => 'How To Code']);

        $this->expectException(\TypeError::class);

        Chat::createConversation($coding_book);
    }

    public function test_chat_facade_throws_exception_when_conversation_created_with_non_participants_array(): void
    {
        $coding_book = Book::factory()->create(['name' => 'How To Code']);

        $this->expectException(\InvalidArgumentException::class);

        Chat::conversations()->create([$coding_book]);
    }

    public function test_chat_throws_exception_when_conversation_created_with_non_participants_array(): void
    {
        $coding_book = Book::factory()->create(['name' => 'How To Code']);

        $this->expectException(\InvalidArgumentException::class);

        Chat::createConversation([$coding_book]);
    }

    public function test_chat_facade_throws_exception_when_conversation_created_with_non_participants_collection(): void
    {
        $books = Book::factory(2)->create();

        $this->expectException(\InvalidArgumentException::class);

        Chat::conversations()->create($books);
    }

    public function test_chat_throws_exception_when_conversation_created_with_non_participants_collection(): void
    {
        $books = Book::factory(2)->create();

        $this->expectException(\InvalidArgumentException::class);

        Chat::createConversation($books);
    }

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

    public function test_chat_adds_array_of_conversations_participants_after_conversation_creation(): void
    {
        $ange = User::factory()->create(['name' => 'Ange']);
        $arsene = User::factory()->create(['name' => 'Arsene']);

        $participants = [$ange, $arsene];

        $conversation1 = Chat::createConversation();
        $conversation2 = Chat::conversations()->create();

        Chat::conversations()->set($conversation1)->add($participants);
        Chat::conversations()->set($conversation2)->add($participants);

        $this->checkParticipantsOverConversations(
            $participants, [$conversation1, $conversation2]
        );
        $this->assertCount(count($participants), $conversation1->participants);
        $this->assertCount(count($participants), $conversation2->participants);
    }

    public function test_chat_adds_collection_of_conversations_participants_after_conversation_creation(): void
    {
        $participants = User::factory(2)->create();

        $conversation1 = Chat::createConversation();
        $conversation2 = Chat::conversations()->create();

        Chat::conversations()->set($conversation1)->add($participants);
        Chat::conversations()->set($conversation2)->add($participants);

        $this->checkParticipantsOverConversations(
            $participants, [$conversation1, $conversation2]
        );
        $this->assertCount(count($participants), $conversation1->participants);
        $this->assertCount(count($participants), $conversation2->participants);
    }

    public function test_chat_throws_exception_when_non_conversation_participant_model_is_parsed_to_remove(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book1 = Book::factory()->create();

        $participants = [$user1, $user2];
        $conversation = Chat::createConversation();

        $this->expectException(\TypeError::class);

        Chat::conversations()
            ->set($conversation)
            ->add($participants)
            ->remove($book1);
    }

    public function test_chat_throws_exception_when_non_conversation_participant_array_is_parsed_to_remove(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book1 = Book::factory()->create();

        $participants = [$user1, $user2];
        $conversation = Chat::createConversation();

        $this->expectException(\InvalidArgumentException::class);

        Chat::conversations()
            ->set($conversation)
            ->add($participants)
            ->remove([$book1]);
    }

    public function test_chat_throws_exception_when_non_conversation_participant_collection_is_parsed_to_remove(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $books = Book::factory(2)->create();

        $participants = [$user1, $user2];
        $conversation = Chat::createConversation();

        $this->expectException(\InvalidArgumentException::class);

        Chat::conversations()
            ->set($conversation)
            ->add($participants)
            ->remove($books);
    }

    public function test_chat_removes_model_of_conversations_participants_after_conversation_creation(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $participants = [$user1, $user2];

        $conversation = Chat::createConversation();

        Chat::conversations()
            ->set($conversation)
            ->add($participants)
            ->remove($user2);

        $this->checkParticipantsOverConversations($user1, $conversation);
        $this->assertCount(1, $conversation->participants);
    }

    public function test_chat_removes_array_of_conversations_participants_after_conversation_creation(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $participants = [$user1, $user2];

        $conversation = Chat::createConversation();

        Chat::conversations()
            ->set($conversation)
            ->add($participants)
            ->remove($participants);

        $this->assertCount(0, $conversation->participants);
    }

    public function test_chat_removes_collection_of_conversations_participants_after_conversation_creation(): void
    {
        $participants = User::factory(2)->create();

        $conversation = Chat::createConversation();

        Chat::conversations()
            ->set($conversation)
            ->add($participants)
            ->remove($participants);

        $this->assertCount(0, $conversation->participants);
    }
}
