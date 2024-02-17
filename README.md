# Model Properties

As a developer, you may have experienced the cognitive load of dealing with arrays to define various model behaviors in Laravel, such as fillable, guarded, casting, default values, and hidden properties. I found myself wondering if there was a more streamlined way to handle this. What if we could use PHP attributes on the class itself to define these properties? This idea led me to explore this approach, which I share in [this video](https://www.youtube.com/watch?v=n8-SHq4zSr4)

## Support Me

Buy me a coffee or something, but more importantly would be if you used this and like it, if you'd talk about this and also tell me if you have any issues with it.

## Installation


```bash
composer require joecianflone/modelproperties
```


## Usage

Here's a standard `User` model where you've got some fillable fields and a few casts and whatnot:

```php
class User extends Model {

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'id' => 'string',
        'role' => UserRole::class
        'is_active' => 'boolean',
        'email_activated_at' => 'datetime',
        'remember_token' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'role' => UserRole::STANDARD
        'is_active' => true,
    ];
}
```

Again, my gripe here is that it takes a bit of work to understand all the fields this model has. It's pretty easy to miss that the `id` is a string if you were looking quickly...and oh crap, I forgot to turn off auto-incrementing did you catch that? How about the fact that the password should be cast and I forgot that one too

So using the model properties it would now look like this:

```php
#[ModelProperties(
    id:  ['string', 'is:primary' 'auto-increment' => false],         
    name:  ['string', 'is:fillable'],      
    email:  ['string', 'is:fillable'],
    password:  ['hashed', 'is:hidden|fillable'],   
    role: [UserRole::class => UserRole::STANDARD], 
    is_active:          ['boolean' => true],
    remember_token:     ['string', 'is:hidden'],
    email_verified_at:  ['datetime'],
    created_at:         ['datetime'],
    updated_at:         ['datetime'],
)]
class User extends Model {

    use HasModelProperties;

}
```

That's it. Add the `HasModelProperties` trait and you're good to start using the `ModelProperties` attribute and have a better DX. 

## Config Settings

Model properties has a few configurations you can change and they're publishable.

```bash
$ php artisan vendor:publish --provider="JoeCianflone\ModelProperties\ModelPropertiesServiceProvider"
```

### Mass Assignment

Turn this off at your own risk! By default, this will set `$guarded = ['*']` you should leave it this way. 

```php
    [
         'mass_assignment_protection' => true, // default
    ]
```    

### Default Mass Assignment Mode

Now if you don't want to set all your properties to either guareded or fillable on all your models you can change this to be either 'guarded' or 'fillable' and then you don't have to worry about being explicit.

NOTE: with `mass_assignment_protection` turned on, you really never have to expliclty set something to `guarded` they'll really be discarded because `$guarded = ['*']`. If you DO turn off `mass_assignment_protection` you're going to NEED to set this to guarded or else we'll throw an exception with this set to either 'none' or 'fillable'. 

*Honestly, think twice before doing that. Your best bet here is to leave mass_assignment_protection set to true and, if anything, set this to fillable. That's basically Laravel's default behavior and setting all the props over to the fillable array.*
    
```php
    [
        // values could be 'none' | 'fillable' | 'guarded'
        'default_property_assignment' => 'none', // default 
    ]
```

### Explicitly Cast Strings

```php
    [
        'explicity_cast_strings' => false // default
    ]
```

In the above examples, see how how told the model that some properties were strings? You don't actually need to cast strings as strings in laravel, but some people like doing that, so if you'd like to be explicit, set this to true. 

## How it works

Under the hood, we're not doing too many fancy things. At the end of the day, the attribute will get transformed into a PHP object and we "just use" all the standard Laravel model variables to populate fillable, guarded and whatever else. The only magic here, if there is any, is Laravels own built-in ability to automatically execute a method in a trait.   

So what is this doing? Mostly parsing the attributes and dumping them in the right places. It adds no extra cruft to the model and if you were to `dd()` a model that uses `ModelProperties` and a model that does not, you'd see no difference in the object created. 

## Testing

This plugin uses Pest for our Unit and Architecture tests, if you'd like to run them yourself you can use...

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities. 

## Credits

-   [Joe Cianflone](https://github.com/JoeCianflone)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
