This **cake3 branch** is for CakePHP 3 compatibility. 

---

# Assets plugin for CakePHP

## What the plugin does

This plugin helps you to manage backend Assets which can be of any filetype. Assets are 
saved in a non-public folder and are handled by an AssetsTable. 

You can create AssetEntities with useful methods:

### General methods

* Check `$asset->exists()` is the uploaded file still is there.
* `$asset->getFileSizeInfo()` prints a human-readable filesize.

### Image rendering 

* Call `echo $asset->getImage()->scaleWidth(350)->toWebP()->addCSS('my-class')` to render a WebP-thumbnail as HTML. 
* The rendered image will be saved in `webroot/img/modified` depending on the modifications and passed options. 
* Pass your custom InterventionImage Filters through `ImageAsset::applyFilter()`
* You can call any InterventionImage API method through `ImageAsset::modify()`

### CSV preview 

* You can use the `TextAssetPreviewHelper` to render tables from CSV-files. 

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

Your uploaded files will be saved in the `assets_assets` table. Run the Migrations
to create the table: 
```
bin/cake migrations migrate -p Assets
```

## Uploading Files

### AssetsController

You can directly upload files using the AssetsController. By default, you can access
the index, view, and edit-methods through an `Admin` prefix. 

### Use Alibi names 

You can use the Assets table to upload files from any model, say you have a `users` table
and need a user image:
1. Create a field `userimage_id` in the `users` table.
2. Run the migrations. 
3. Now you need to tell CakePHP that your `Userimage` is not a real Entity, but comes from the `Assets.AssetsAssetsTable` (sorry) table: 

```
in \App\Model\Table\Users: 

$this->belongsTo('Userimages')
    ->setForeignKey('userimage_id')
    ->setClassName('Assets.AssetsAssets');
```

You can now upload your Userimage e.g. in `Users/add.php`: 
```
<?= $this->Form->create($magazine, ['type' => 'file']) ?>
    ...
    ...
    <?= $this->Form->control('userimage.filename', ['options' => $userimages, 'empty' => true]) ?>
    ...
<?= $this->Form->end() ?>
```

In order for this to work, the relation should be set properly, e.g. through `bin/cake bake users`, so that 
CakePHP knows that your `$user->userimage` is an `AssetsAsset` Entity. Note that you have to contain
`Userimages` in your Controller or Table's finder methods: 

```
$users = $this->Users->find()->contain([
        'Userimages',
        'Addresses,
        '...
    ]);
    
$image = $users->first()->userimage?->getImage();
```

The Assets table won't save where the file came from. You can use the title, description, or category field to keep track of that. 

You can inject custom behaviors into the Assets table to hook into Event Handlers, or to create custom finder methods. 

In your `app.php`, you can override the default settings: 
```
/**
 * Configuration for the Assets Plugin
 */
'AssetsPlugin' => [
    'AssetsTable' => [
        'DisplayField' => 'description', 
        'Behaviors' => [
            'MyFinderBehavior',
            'CategoryCheckBehavior',
        ]
    ]
],
```

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

## Feedback, Bugs, and Feature Requests

This plugin was no plugin from the beginning and we are working on making it usable now among different CakePHP installations. 

As this package is in early development as a plugin, we don't expect it to fit in for a lot of use cases right now.  

If you have questions or problems with the plugin, you can also contact me via email:  
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
