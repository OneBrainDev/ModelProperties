<?php

namespace JoeCianflone\ModelProperties\Tests\Unit;

use Illuminate\Support\Facades\Config;
use JoeCianflone\ModelProperties\Exceptions\MassAssignmentVulnerabilityException;
use JoeCianflone\ModelProperties\ModelProperties;

it('has fillable properties', function (ModelProperties $modelProps) {
    $fillable = $modelProps->getFillable();

    expect($fillable)->toBeArray();
    expect($fillable)->toContain('name');
    expect($fillable)->toContain('all_things');
    expect($fillable)->not->toContain('foo');
    expect($fillable)->not->toContain('password');
})->with('ModelProps');

it('has guarded properties, no default assignment', function (ModelProperties $modelProps) {
    Config::set('modelproperties.mass_assignment_protection', false);
    Config::set('modelproperties.default_property_assignment', 'none');

    $guarded = $modelProps->getGuarded();

    expect($guarded)->toBeArray();
    expect($guarded)->toContain('foo');
    expect($guarded)->toContain('address');
    expect($guarded)->not->toContain('name');
    expect($guarded)->not->toContain('password');
})->with('ModelProps');

it('has default value set', function (ModelProperties $modelProps) {
    $defaults = $modelProps->getDefaultValues();

    expect($defaults)->toBeArray();
    expect($defaults)->toHaveKey('active', true);
})->with('ModelProps');

it('does not cast strings as strings', function (ModelProperties $modelProps) {
    Config::set('modelproperties.explicity_cast_strings', false);
    $defaults = $modelProps->getCasts();

    expect($defaults)->toBeArray();
    expect($defaults)->toHaveLength(1);
})->with('ModelProps');

it('explicitly cast strings as strings', function (ModelProperties $modelProps) {
    Config::set('modelproperties.explicity_cast_strings', true);
    $defaults = $modelProps->getCasts();

    expect($defaults)->toBeArray();
    expect($defaults)->toHaveLength(6);
})->with('ModelProps');

it('has explicitly hidden and visible properties', function (ModelProperties $modelProps) {
    $hidden = $modelProps->getHidden();
    $visible = $modelProps->getVisible();

    expect($hidden)->toContain('password', 'all_things');
    expect($visible)->toContain('address');
})->with('ModelProps');

it('does not override primary key data', function (ModelProperties $modelProps) {
    expect($modelProps->hasPrimaryKey())->toBe(false);
})->with('ModelProps');

it('sets a primary key uses default data', function (ModelProperties $modelProps) {
    expect($modelProps->hasPrimaryKey())->toBe(true);
    expect($modelProps->getPrimaryKeyName())->toBe('id');
    expect($modelProps->getPrimaryKeyType())->toBe('int');
    expect($modelProps->isPrimaryKeyIncrementing())->toBe(true);
})->with('ModelPropsWithKey');

it('sets a primary key with custom name', function (ModelProperties $modelProps) {
    expect($modelProps->hasPrimaryKey())->toBe(true);
    expect($modelProps->getPrimaryKeyName())->toBe('mypk');
    expect($modelProps->getPrimaryKeyType())->toBe('int');
    expect($modelProps->isPrimaryKeyIncrementing())->toBe(true);
})->with('ModelPropsCustomPrimaryKey');

it('sets a primary key with custom name and settings', function (ModelProperties $modelProps) {
    expect($modelProps->hasPrimaryKey())->toBe(true);
    expect($modelProps->getPrimaryKeyName())->toBe('mypk');
    expect($modelProps->getPrimaryKeyType())->toBe('string');
    expect($modelProps->isPrimaryKeyIncrementing())->toBe(false);
})->with('ModelPropsCustomPrimaryKeyAsUUID');

it('defaults to fillable and contains non-explicit properties', function (ModelProperties $modelProps) {
    Config::set('modelproperties.default_property_assignment', 'fillable');
    $fillable = $modelProps->getFillable();

    expect($fillable)->toBeArray();
    expect($fillable)->toContain('name');
    expect($fillable)->toContain('all_things');
    expect($fillable)->toContain('password');
    expect($fillable)->not->toContain('foo');
    expect($fillable)->toHaveLength(4);

})->with('ModelProps');

it('defaults to guarded and contains non-explicit properties', function (ModelProperties $modelProps) {
    Config::set('modelproperties.default_property_assignment', 'guarded');
    $guarded = $modelProps->getGuarded();
    expect($guarded)->toBeArray();
    expect($guarded)->toContain('*');
    expect($guarded)->toHaveLength(1);
})->with('ModelProps');

it('has mass assignment protection, but no default properties set', function (ModelProperties $modelProps) {
    Config::set('modelproperties.default_property_assignment', 'none');
    $guarded = $modelProps->getGuarded();
    $fillable = $modelProps->getFillable();

    expect($guarded)->toBeArray();
    expect($fillable)->toBeArray();
    expect($guarded)->toContain('*');
    expect($guarded)->toHaveLength(1);
    expect($fillable)->toHaveLength(0);

})->with('ModelPropsWithDefaults');

it('throws a MassAssignmentVulnerabilityException', function () {
    Config::set('modelproperties.mass_assignment_protection', false);

    Config::set('modelproperties.default_property_assignment', 'none');
    expect(fn () => new ModelProperties())->toThrow(MassAssignmentVulnerabilityException::class);

    Config::set('modelproperties.default_property_assignment', 'fillable');
    expect(fn () => new ModelProperties())->toThrow(MassAssignmentVulnerabilityException::class);
});
