<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Concerns;

use AngeArsene\Chat\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use AngeArsene\Chat\Contracts\ConversationParticipantInterface;

trait NormalizesParticipantsOrConversations
{
    /**
     * Normalize participants to array, keeping null values.
     */
    protected function normalize(
        null |
        array |
        Collection |
        Conversation |
        ConversationParticipantInterface $normalized
    ): null|array|Collection {
        if ($normalized === null) {
            return null;
        }
        
        if (is_array($normalized) || $normalized instanceof Collection) {
            if (empty($normalized)) {
                throw new \EmptyIterator;
            }
            return $normalized;
        }
        
        return [$normalized];
    }
    
    /**
     * Normalize participants to array, converting null to empty array.
     */
    protected function normalizeNotToNull(
        array |
        Collection |
        Conversation |
        ConversationParticipantInterface $normalized
    ): array|Collection {
        return $this->normalize($normalized);
    }
}