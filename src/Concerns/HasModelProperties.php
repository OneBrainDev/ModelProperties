<?php declare(strict_types=1);

namespace JoeCianflone\ModelProperties\Concerns;

use Crell\AttributeUtils\Analyzer;
use JoeCianflone\ModelProperties\ModelProperties;

trait HasModelProperties
{
    public function initializeHasModelProperties(): void
    {
        $properties = (new Analyzer())->analyze(__CLASS__, ModelProperties::class);

        if ($properties->hasPrimaryKey()) {
            $this->primaryKey = $properties->getPrimaryKeyName();
            $this->keyType = $properties->getPrimaryKeyType();
            $this->incrementing = $properties->isPrimaryKeyIncrementing();
        }

        $this->fillable($properties->getFillable());
        $this->guard($properties->getGuarded());
        $this->setHidden($properties->getHidden());
        $this->mergeCasts($properties->getCasts());

        $this->attributes = $properties->getDefaultValues();
    }
}
