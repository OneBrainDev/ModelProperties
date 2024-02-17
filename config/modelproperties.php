<?php

/**
 * if default_property_assignment = fillable -- default all props to the fillable array
 * if default_property_assignment = guarded -- default all props to guarded
 * if default_property_assignment = none -- default all props to nothing
 */
return [
    // turn this off at your own risk!
    'mass_assignment_protection' => true,

    // with mass_assignment_protection turned on, each model has their $guared array = [*] so
    // now if you don't want to set all your properties to either guareded or fillable on
    // all your models you can change this to be either 'guarded' or 'fillable' and
    // then you don't have to worry about setting every prop.

    // NOTE: with mass_assignment turned on, you really never have to expliclty set something to guared
    // they'll really be discarded because $guarded = [*]. If you DO turn off mass_assignment you're
    // going to NEED to set this to guarded or else we'll throw an exception with this set to
    // either 'none' or 'fillable'. Honestly, think twice before doing that. Your best bet
    // here is to leave mass_assignment set to true and, if anything, set this to
    // fillable. That's basically Laravel's default behavior and setting all
    // the props over to the fillable array.
    'default_property_assignment' => 'none',

    // Show that a property is a string, but don't explicityly cast it, because
    // you don't need to do a string cast by default, if you WANT to see all
    // your attributes casted, even the string ones, change this to true
    'explicity_cast_strings' => false,
];
