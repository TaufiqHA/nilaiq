<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'chat_id',
    'telegram_username',
    'linked_at',
    'last_active_at',
])]
class TelegramLink extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'chat_id' => 'integer',
            'linked_at' => 'datetime',
            'last_active_at' => 'datetime',
        ];
    }

    /**
     * Get the user linked to this Telegram chat.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
