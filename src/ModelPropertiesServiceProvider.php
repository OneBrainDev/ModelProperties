<?php declare(strict_types=1);

namespace JoeCianflone\ModelProperties;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class ModelPropertiesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/ModelProperties.php' => config_path('modelproperties.php'),
        ]);

        Collection::macro('getKeysFromGroup', function (string $keys, string $groupBy): Collection {
            /** @var Collection $this */
            $group = $this
                ->groupBy($groupBy, true)
                ->get($keys)
                ?->keys();

            return $group ?? collect([]);
        });

        Collection::macro('mapNamesToValues', function (string $keyValue): Collection {
            /** @var Collection $this */
            return $this
                ->flatMap(
                    fn (array $propAttribute, string $propName) => collect($propAttribute)->flatMap(
                        fn (mixed $attrValue, string $attrKey) => match ($attrKey === $keyValue) {
                            true => [$propName => $attrValue],
                            false => [],
                        }
                    )
                );
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/modelproperties.php', 'modelproperties'
        );
    }
}
