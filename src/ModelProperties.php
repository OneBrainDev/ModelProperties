<?php declare(strict_types=1);

namespace JoeCianflone\ModelProperties;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JoeCianflone\ModelProperties\Contracts\EloquentModelProperties;
use JoeCianflone\ModelProperties\Exceptions\InvalidConfigException;
use JoeCianflone\ModelProperties\Exceptions\MassAssignmentVulnerabilityException;
use JoeCianflone\ModelProperties\Mappers\MapAttributesToProperties;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class ModelProperties implements EloquentModelProperties
{
    private array $pkAttributes;

    private string $pkName;

    private Collection $props;

    public function __construct(mixed ...$attrs)
    {
        try {
            $this->configIsValid();
            $this->props = (new MapAttributesToProperties($attrs))->getProperties();

            $pkCollectionQuery = $this->props->where('key', '=', 'primary');
            $this->pkName = is_null($pkCollectionQuery->keys()->first()) ? 'id' : (string) $pkCollectionQuery->keys()->first();
            $this->pkAttributes = $pkCollectionQuery->first() ?? [];
        } catch (InvalidConfigException $e) {
            throw new MassAssignmentVulnerabilityException($e->getMessage());
        }
    }

    public function getCasts(): array
    {
        return $this->props
            ->mapNamesToValues('type')
            ->filter(function ($value) {
                return (! config('modelproperties.explicity_cast_strings'))
                    ? $value !== 'string'
                    : $value;
            })
            ->toArray();
    }

    public function getDefaultValues(): array
    {
        return $this->props
            ->mapNamesToValues('value')
            ->toArray();
    }

    public function getFillable(): array
    {
        return $this->getMassAssignedPropsOfType('fillable');
    }

    public function getGuarded(): array
    {
        if (config('modelproperties.mass_assignment_protection')) {
            return ['*'];
        }

        return $this->getMassAssignedPropsOfType('guarded');
    }

    public function getHidden(): array
    {
        return $this->props
            ->getKeysFromGroup(keys: 'hidden', groupBy: 'serialize')
            ->toArray();
    }

    public function getPrimaryKeyName(): string
    {
        return $this->pkName;
    }

    public function getPrimaryKeyType(): string
    {
        return match (Arr::get($this->pkAttributes, 'type')) {
            null, 'int', 'integer' => 'int',
            default => 'string',
        };
    }

    public function getVisible(): array
    {
        return $this->props
            ->getKeysFromGroup(keys: 'visible', groupBy: 'serialize')
            ->toArray();
    }

    public function hasPrimaryKey(): bool
    {
        return count($this->pkAttributes) > 0;
    }

    public function isPrimaryKeyIncrementing(): bool
    {

        if (Arr::get($this->pkAttributes, 'increment')) {
            return Arr::get($this->pkAttributes, 'increment');
        }

        return match ($this->getPrimaryKeyType()) {
            'int' => true,
            default => false,
        };
    }

    private function configIsValid(): void
    {
        // Why? If you've set mass assignment to false, you're opening yourself up to a Mass Assignment Vulnerability
        // At the same time, if you've set your default property assignment to fillable or none, you're setting
        // now saying "please don't do anything for my properties by default" and also setting "guarded" to
        // be an empty array...which means you're really setting yourself up for a bad day so, I'm not
        // gonna allow that to happen. Don't like it? Fork it and do dumb shit on your own version
        if ((! config('modelproperties.mass_assignment_protection'))
            && (config('modelproperties.default_property_assignment') === 'fillable' || config('modelproperties.default_property_assignment') === 'none')) {
            throw new InvalidConfigException("You've disabled `mass_assignment_protection` and set the `default_property_assignment` to '".config('modelproperties.default_property_assignment')."'. In config/modelproperties.php set `mass_assignment_protection` to true or set `default_property_assignment` to 'guarded'");
        }
    }

    private function getMassAssignedPropsOfType(string $type): array
    {
        return $this->props
            ->getKeysFromGroup(keys: $type, groupBy: 'as')
            ->when(config('modelproperties.default_property_assignment') === $type, function (Collection $props) {
                $defaultAssignment = $this->props->filter(
                    fn (array $v) => ! Arr::exists($v, 'as')
                )->keys()->toArray();

                $props->push(...$defaultAssignment);
            })->toArray();
    }
}
