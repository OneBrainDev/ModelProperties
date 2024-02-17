<?php

use JoeCianflone\ModelProperties\ModelProperties;

dataset('ModelProps', [
    fn () => new ModelProperties(
        name: ['string', 'is:fillable'],
        foo: ['string', 'is:guarded'],
        active: ['boolean' => true],
        password: ['string', 'is:hidden'],
        address: ['string', 'is:visible|guarded'],
        all_things: ['string' => 'cheese', 'is:fillable|hidden']
    ),
]);

dataset('ModelPropsWithDefaults', [
    fn () => new ModelProperties(
        name: ['string'],
        foo: ['string'],
        active: ['boolean' => true],
        password: ['string', 'is:hidden'],
        address: ['string', 'is:visible'],
        all_things: ['string' => 'cheese', 'is:hidden']
    ),
]);

dataset('ModelPropsWithKey', [
    fn () => new ModelProperties(
        id: ['is:primary'],
    ),
]);

dataset('ModelPropsCustomPrimaryKey', [
    fn () => new ModelProperties(
        mypk: ['is:primary'],
    ),
]);

dataset('ModelPropsCustomPrimaryKeyAsUUID', [
    fn () => new ModelProperties(
        mypk: ['uuid', 'is:primary'],
    ),
]);
