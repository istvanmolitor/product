# Product modul

Termékek kezelése

## Előfeltételek

Telepíteni kell a következő modulokat.:
- https://gitlab.com/molitor/language
- https://gitlab.com/molitor/currency
- https://gitlab.com/molitor/user
- https://gitlab.com/molitor/file

## Telepítés

### Provider regisztrálása
config/app.php
```php
'providers' => ServiceProvider::defaultProviders()->merge([
    /*
    * Package Service Providers...
    */
    \Molitor\Product\Providers\ProductServiceProvider::class,
])->toArray(),
```

### Seeder regisztrálása

database/seeders/DatabaseSeeder.php
```php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
```

### Menüpont megjelenítése az admin menüben

Ma a Menü modul telepítve van akkor meg lehet jeleníteni az admin menüben.

```php
<?php
//Menü builderek listája:
return [
    \Molitor\Product\Services\Menu\ProductMenuBuilder::class
];
```

### Breadcrumb telepítése

A modul breadcrumbs.php fileját regisztrálni kell a configs/breadcrumbs.php fileban.
```php
<?php
'files' => [
    base_path('/vendor/molitor/product/src/routes/breadcrumbs.php'),
],
```
