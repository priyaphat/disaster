<?php

namespace App\Casts;

use App\Enums\ShelterUtilities;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UtilitiesCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $utilities = json_decode($value, true) ?? [];
        return collect($utilities)->map(function ($utility) {
            return [
                'id' => (int) $utility['id'],
                'name' => ShelterUtilities::tryFrom($utility['id'])->utility(),
                'is_active' => (bool) $utility['is_active'],
                'description' => $utility['description'] ?? null,
            ];
        })->toArray();
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return json_encode($value);
    }
}
