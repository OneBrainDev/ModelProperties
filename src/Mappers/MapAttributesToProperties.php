<?php declare(strict_types=1);

namespace JoeCianflone\ModelProperties\Mappers;

use Illuminate\Support\Collection;

final class MapAttributesToProperties
{
    private readonly Collection $properties;

    public function __construct(
        private readonly array $attributes
    ) {
        $this->properties = $this->transform($this->attributes);
    }

    public function getProperties(): Collection
    {
        return $this->properties ?? collect([]);
    }

    public function transform(array $attrs): Collection
    {
        return collect($attrs)->flatMap(function (array $attributeSet, string $propName) {
            $propArray = collect($attributeSet)->flatMap(
                fn (mixed $attribute, int|string $key) => match (is_int($key)) {
                    true => $this->mapAttributeSetKeyValuePairs($attribute),
                    false => $this->mapKeyValuePairs($key, $attribute)
                }
            )->toArray();

            return [$propName => $propArray];
        });
    }

    private function mapAttributeSetKeyValuePairs(mixed $attributeSet): array
    {
        $attributePair = explode(':', $attributeSet);

        if (count($attributePair) > 1) {
            return collect(explode('|', $attributePair[1]))
                ->flatMap(fn ($v, $k) => $this->mapKeyValuePairs($v))
                ->toArray();
        }

        return $this->mapKeyValuePairs('type', $attributePair[0]);
    }

    private function mapKeyValuePairs(string $key, mixed $attribute = null): array
    {
        return match (true) {
            ($key === 'type') => [$key => $attribute],
            ($key === 'primary') => ['key' => $key],
            ($key === 'guarded'), ($key === 'fillable') => ['as' => $key],
            ($key === 'visible'), ($key === 'hidden') => ['serialize' => $key],
            ($key === 'auto-increment') => ['increment' => $attribute],
            default => [
                'type' => $key,
                'value' => $attribute,
            ],
        };
    }
}
