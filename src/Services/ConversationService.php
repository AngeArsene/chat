<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Services;

use AngeArsene\Chat\Models\Conversation;
use AngeArsene\Chat\Models\Participation;
use Illuminate\Database\Eloquent\Collection;
use AngeArsene\Chat\Contracts\ConversationParticipantInterface;
use AngeArsene\Chat\Concerns\NormalizesParticipantsOrConversations;

final class ConversationService
{
    use NormalizesParticipantsOrConversations;

    public function __construct(
        private Conversation $conversation,
        private Participation $participation
    ) {
    }

    public function getById(Conversation | int $conversation): Conversation
    {
        return $this->conversation->find(
            is_int($conversation)
                ? $conversation
                : $conversation->id
        );
    }

    public function create(
        null |
        array |
        Collection |
        ConversationParticipantInterface $participants = null
    ): Conversation {
        $conversation = $this->conversation->create();

        $this->set($conversation)->add($participants);

        return $conversation;
    }

    public function set(Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function add(
        null |
        array |
        Collection |
        ConversationParticipantInterface $participants = null
    ): self {
        $participants = $this->normalize($participants);

        if ($participants !== null) {
            $participations = [];
            foreach ($participants as $participant) {
                if (!($participant instanceof ConversationParticipantInterface)) {
                    throw new \InvalidArgumentException;
                }

                $participations[] = [
                    'conversation_id' => $this->conversation->getKey(),
                    'messageable_id' => $participant->getKey(),
                    'messageable_type' => get_class($participant),
                ];
            }
            $this->participation->insert($participations);
        }

        return $this;
    }

    public function remove(
        null |
        array |
        Collection |
        ConversationParticipantInterface $participants = null
    ): self {
        $participants = $this->normalize($participants);

        if ($participants !== null) {
            foreach ($participants as $participant) {
                if (!($participant instanceof ConversationParticipantInterface)) {
                    throw new \InvalidArgumentException;
                }

                $this->participation->where([
                    'conversation_id' => $this->conversation->getKey(),
                    'messageable_id' => $participant->getKey(),
                    'messageable_type' => get_class($participant),
                ])->delete();
            }
        }

        return $this;
    }
}
