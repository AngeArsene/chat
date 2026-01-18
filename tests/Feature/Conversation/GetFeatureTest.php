<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Tests\Feature\Conversation;

use AngeArsene\Chat\Facades\Chat;
use AngeArsene\Chat\Services\ConversationService;
use AngeArsene\Chat\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(ConversationService::class, 'getById')]
final class AddFeatureTest extends TestCase
{
    public function test_chat_conversations_returns_conversation_by_id(): void
    {
        $conv = Chat::conversations()->create();

        $conversation1 = Chat::conversations()->getById($conv);
        $conversation2 = Chat::conversations()->getById($conv->id);

        $this->assertEquals($conv->id, $conversation1->id);
        $this->assertEquals($conv->id, $conversation2->id);
    }
}