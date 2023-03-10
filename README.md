# Assets plugin for CakePHP

## What the plugin does

This plugin helps you to manage backend Assets which can be of any filetype. Assets are 
saved in a non-public folder and are handled by an AssetsTable. 

You can create AssetEntities with useful methods:

### General methods

* Check if the uploaded file still is there: `$asset->exists() // true or false` 
* `$asset->getFileSizeInfo()` prints a human-readable filesize.
* Get a link to the AssetsController::download() (Admin-prefixed): `$asset->getDownloadLink()`

### Image rendering 

* Call `echo $asset->getImage()->scaleWidth(350)->toWebP()->addCSS('my-class')` to render a WebP-thumbnail as HTML. 
* The rendered image will be saved in `webroot/img/modified` depending on the modifications and passed options. 
* Pass your custom InterventionImage Filters through `ImageAsset::applyFilter()`
* You can call any InterventionImage API method through `ImageAsset::modify()`

### CSV preview 

* You can use the `TextAssetPreviewHelper` to render tables from CSV-files. 
* Loop through rows in a csv-Asset: `foreach ($asset->getReader()->getRecords() as $row) ...`

### Alibi-properties for any model

* `$asset` can be `$user->image`, `$company->logo` or your latest CSV import. Just configure a `belongsTo` relation on your model and set the ClassName.

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require passchn/cakephp-assets
```

Load the plugin: 
```
 bin/cake plugin load Assets
```

Run the Migrations to create the plugin tables: 
```
bin/cake migrations migrate -p Assets
```

Now you should have an `assets_assets` and an `assets_phinxlog` table in your database. 

## Uploading Files

### AssetsController

You can directly upload files using the AssetsController. By default, you can access
the index, view, and edit-methods through an `Admin` prefix: `your-app.test/admin/assets/assets` 

### Use Alibi names 

You can use the Assets table to upload files from any model, say you have a `users` table
and need a user image:
1. Create a field `userimage_id` in the `users` table as nullable `char(36)` (UUID).
2. Run the migrations: `bin/cake bake users`
3. Now you need to tell CakePHP that your `Userimage` is not a real Entity, but comes from the `Assets.AssetsTable` table: 

```
in \App\Model\Table\Users: 

$this->belongsTo('Userimages')
    ->setForeignKey('userimage_id')
    ->setClassName('Assets.Assets');
```
> This does not work anymore. The `cakephp-bake` plugin from version `2.8` onwards seems to detect that you don't actually have a `userimages` table and will not assume a `belongsTo` relation. You will have to write the code yourself or downgrade to version `2.7`. 

You can now put an upload field for your Userimage e.g. in `templates/Users/edit.php`: 
```
<?= $this->Form->create($user, ['type' => 'file']) ?>
    ...
    ...
    <?= $this->Form->control('userimage.filename', [
        'type' => 'file', 
        'label' => 'Upload a profile photo',
    ]) ?>
    ...
<?= $this->Form->end() ?>
```

In order for this to work, the relation should be set properly, e.g. through `bin/cake bake users`, so that 
CakePHP knows that your `$user->userimage` is an `Asset` Entity. Note that you have to contain
`Userimages` in your Controller or Table's finder methods: 

```
$users = $this->Users->find()->contain([
        'Userimages',
        'Addresses,
        '...
    ]);
    
$image = $users->first()->userimage?->getImage();
```

The AssetsTable does not know which model the uploaded file belongs to. You can use the `title`, `description`, or `category` field to keep track of that. 

To do this, you can inject custom behaviors into the AssetsTable to hook into Event Handlers (e.g., set the `title` on `beforeSave()`), or to create custom finder methods, e.g. by your defined categories. 

In your config file `app.php`, `app_local.php`, or `app_assets.php`, you can override the [default settings](https://github.com/passchn/cakephp-assets/blob/master/config/app_assets.php) and add custom Behaviors.

## File manipulation 

### Images 

If an Asset is an image (the MimeType starts with `image/*`), you can call 
```
$asset->getImage(); 
```

This returns an instance of the `Assets\Utilities\ImageAsset` class, which offers a wrapper API for `ImagineImage`. 

For example, you can call: 
```
echo $asset->getImage()->scaleWidth(350)->toWebP()->getPath()
```

This will generate a scaled version of your original file, converted to WebP, and return the relative path (in `/webroot`) to the file. 

You might want to output html directly: 

```
<?= $asset->getImage()->scaleWidth(520)->toJpg()->setCSS('my-class') ?>
```

This will call the `ImageAsset::__toString()` method and will return HTML with your set class,
an alt-parameter with the Asset's title and correct `width` and `height` params. 

Image manipulation is done through Intervention with GD as default, but you can enable imagick through your app.php config. 

#### Rendering of `<picture>` elements

Load the PictureHelper in your `AppView.php`: 
```
$this->loadHelper('Assets.Picture');
```
In a template, pass an `ImageAsset` to the Helper as well as an array of widths and params:  

```
<?= $this->Picture->webp(
    $user->photo->modify('resize', 350, 600),
    [150, 350],
    [
        'class' => 'user-img',
        'width' => 350,
        'height' => 600,
        'sizes' => "(min-width: 230px) 100vw, (min-width: 640px) 50vw"
    ]
) ?>
```

## Feedback, Bugs, and Feature Requests

This plugin was no plugin from the beginning and we are working on making it usable now among different CakePHP installations. 

As this package is in early development as a plugin, we don't expect it to fit in for a lot of use cases right now.  

If you have questions or problems with the plugin, open an Issue on GitHub.  

You can also contact me via email:  
[psc@brandcom.de](mailto:psc@brandcom.de) 

## Packages this plugin uses

Besides CakePHP, the plugin depends on the following packages: 
```
"josegonzalez/cakephp-upload": "^6.0",
"league/csv": "^9.8",
"nette/finder": "^2.5",
"nette/utils": "^3.2",
"intervention/image": "^2.7"
```
