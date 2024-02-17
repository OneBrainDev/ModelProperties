<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('it enforces strict typing')
    ->expect('JoeCianflone\ModelProperties')
    ->toUseStrictTypes();

arch('Concerns folder are all traits')
    ->expect('JoeCianflone\ModelProperties\Concerns')
    ->toBeTraits();

arch('Contracts folder are all interfaces')
    ->expect('JoeCianflone\ModelProperties\Contracts')
    ->toBeInterfaces();

arch('EloquentModelProperites implements only one contract')
    ->expect('JoeCianflone\ModelProperties\ModelProperties')
    ->toOnlyImplement('JoeCianflone\ModelProperties\Contracts\EloquentModelProperties');
