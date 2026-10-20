<?php

namespace App\Rules;

use App\Services\OwnershipService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class OwnedExists implements ValidationRule
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public function __construct(private string $modelClass) {}

    /**
     * Validate that the referenced record exists and belongs to the user.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $record = $this->modelClass::query()->find($value);

        if ($record === null || ! app(OwnershipService::class)->isOwnedBy($record, auth()->user())) {
            $fail('Data :attribute tidak valid atau bukan milik Anda.');
        }
    }
}
