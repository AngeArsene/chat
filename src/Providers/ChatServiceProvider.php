<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Providers;

use AngeArsene\Chat\Chat;
use Illuminate\Support\ServiceProvider;
use AngeArsene\Chat\Models\Conversation;
use AngeArsene\Chat\Models\Participation;
use AngeArsene\Chat\Services\ConversationService;

final class ChatServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
    }

    public function register()
    {
        $this->app->bind('chat', function () {
            return new Chat(new ConversationService(
                new Conversation,
                new Participation
            ));
        });
    }
}
