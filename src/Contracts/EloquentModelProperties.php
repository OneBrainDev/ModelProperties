<?php declare(strict_types=1);

namespace JoeCianflone\ModelProperties\Contracts;

interface EloquentModelProperties
{
    public function getCasts(): array;

    public function getDefaultValues(): array;

    public function getFillable(): array;

    public function getGuarded(): array;

    public function getHidden(): array;

    public function getPrimaryKeyName(): string;

    public function getPrimaryKeyType(): string;

    public function getVisible(): array;

    public function hasPrimaryKey(): bool;

    public function isPrimaryKeyIncrementing(): bool;
}
