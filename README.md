[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Distilleries/Security/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Distilleries/Security/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Distilleries/Security/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Distilleries/Security/?branch=master)
[![Build Status](https://travis-ci.org/Distilleries/Security.svg?branch=master)](https://travis-ci.org/Distilleries/Security)
[![Total Downloads](https://poser.pugx.org/distilleries/Security/downloads)](https://packagist.org/packages/distilleries/Security)
[![Latest Stable Version](https://poser.pugx.org/distilleries/Security/version)](https://packagist.org/packages/distilleries/Security)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md) 

# Security

Security is an admin panel base on laravel 5.6.*
This package give you some implementation do add a content management system of your application.
You can override everything. This Cms give view few tools to develop your content management easily and properly.


## Table of contents

1. [Require](#require)
1. [Installation](#installation)
1. [Configurations](#configurations)
1. [Menu](#menu)
1. [State](#state)
    1. [Datatable](#1-datatable)
    1. [Order](#2-order)
    1. [Export](#3-export)
    1. [Import](#4-import)
    1. [Form](#5-form)
1. [Component](#component)
    1. [Admin BaseComponent](#admin-basecomponent)
    1. [Admin ModelBaseController](#admin-modelbasecontroller)
    1. [AdminBaseController](#admin-basecontroller)
1. [Model](#model)
1. [Global scope](#global-scope)
    1. [Status](#status)
1. [Permissions](#permissions)
1. [Views](#views)
1. [Assets (CSS and Javascript)](#assets-css-and-javascript)
    1. [Sass](#sass)
    1. [Images](#images)
    1. [Javascript](#javascript)
    1. [Gulp](#gulp)
1. [Create a new backend module](#create-a-new-backend-module)
1. [Case studies](#case-studies)
    1. [Generate your migration](#1-generate-your-migration)
    1. [Generate your model](#2-generate-your-model)
    1. [Generate you component](#3-generate-you-component)
    1. [Add your controller in the routes file](#4-add-your-controller-in-the-routes-file)
    1. [Add to the menu](#5-add-to-the-menu)

## Require

To use this project you have to install:

1. Php 7.1.3 or more
3. Active mpcrypt
4. Composer https://getcomposer.org/download/
5. Sass (`gem install sass`)
6. NodeJs version v9.4.0

## Installation

Add on your composer.json

``` json
    "require": {
        "distilleries/Security": "2.*",
    }
```

run `composer update`.


Add Application override to `bootstrap/app.php`:


``` php

$app = new \Distilleries\Security\Fondation\Application(
    realpath(__DIR__ . '/../')
);


$app->bind('path.storage', function ($app) {
    $path = env('STORAGE_PATH', base_path() . DIRECTORY_SEPARATOR . 'storage');

    return $path;
});

```


I add a bind event to override the storage path. If you want overrid it juste add `STORAGE_PATH=` on your .env. If you don't want override it juste to put it on your .env.


Add Service provider to `config/app.php`:

``` php
    'providers' => [
        
        /*
         * Package Service Providers...
         */
        Laravel\Tinker\TinkerServiceProvider::class,
        Distilleries\FormBuilder\FormBuilderServiceProvider::class,
        Distilleries\DatatableBuilder\DatatableBuilderServiceProvider::class,
        Distilleries\PermissionUtil\PermissionUtilServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Distilleries\Security\SecurityServiceProvider::class,
        Distilleries\Security\SecurityRouteServiceProvider::class,


    ]
```

And Facade (also in `config/app.php`) replace the laravel facade `Mail`

``` php
    'aliases' => [
        /**
         * Vendor facade
         *
         */
       
     'FormBuilder'    => \Distilleries\FormBuilder\Facades\FormBuilder::class,
     'Form'           => Collective\Html\FormFacade::class,
     'HTML'           => Collective\Html\HtmlFacade::class,
     'Datatable'      => \Distilleries\DatatableBuilder\Facades\DatatableBuilder::class,
     'PermissionUtil' => \Distilleries\PermissionUtil\Facades\PermissionUtil::class,
     'Excel'          => \Maatwebsite\Excel\Facades\Excel::class,
    ]
```

**Replace the service old facade Mail by the new one.**

Publish the configuration:

```ssh
php artisan vendor:publish --provider="Distilleries\Security\SecurityServiceProvider"
```

## Configurations

```php
    return [
        'login_uri'           => 'admin/login',
        'logout_action'       => 'Distilleries\Security\Http\Controllers\Admin\LoginController@getLogout',
        'admin_base_uri'      => 'admin',
        'config_file_assets'  => base_path().'/package.json',
        'folder_whitelist'    => [
            'moximanager'
        ],
        'listener'            => [
            '\Distilleries\Security\Listeners\UserListener'
        ],
        'mail'                => [
            'actions' => [
                'emails.password'
            ]
        ],
        'menu'                => \Distilleries\Security\Config\MenuConfig::menu([], 'beginning'),
        'menu_left_collapsed' => false,
        'state'               => [
            'Distilleries\DatatableBuilder\Contracts\DatatableStateContract' => [
               'color'    => 'bg-green-haze',
               'icon'     => 'th-list',
               'libelle'  => 'Security::menu.datatable',
               'position' => 0,
               'action'   => 'getIndex'
           ],
           'Distilleries\Security\Contracts\OrderStateContract'           => [
               'color'    => 'bg-grey-cascade',
               'icon'     => 'resize-vertical',
               'libelle'  => 'Security::menu.order_state',
               'position' => 1,
               'action'   => 'getOrder'
           ],
           'Distilleries\Security\Contracts\ExportStateContract'          => [
               'color'    => 'bg-blue-hoki',
               'icon'     => 'save-file',
               'libelle'  => 'Security::menu.export',
               'position' => 2,
               'action'   => 'getExport'
           ],
           'Distilleries\Security\Contracts\ImportStateContract'          => [
               'color'    => 'bg-red-sunglo',
               'icon'     => 'open-file',
               'libelle'  => 'Security::menu.import',
               'position' => 3,
               'action'   => 'getImport'
           ],
           'Distilleries\FormBuilder\Contracts\FormStateContract'           => [
               'color'    => 'bg-yellow',
               'icon'     => 'pencil',
               'libelle'  => 'Security::menu.add_state',
               'position' => 4,
               'action'   => 'getEdit'
           ],
        ]
    ];
```

Field | Usage
----- | -----
login_uri | Uri to access of the login page by default `admin/login`.
admin_base_uri | base of the admin uri `admin` by default.
config_file_assets| File loaded to get the version number of the application. This version number is use to add it of the generated css and javascript to force the reload when you deploy your application.
folder_whitelist | Table of folders accessible to display the assets.
listener | Table of class autoloaded to listen a custom event.
mail.actions | List of action available to send an email. This list is display in email module backend.
menu | Use the method \Distilleries\Security\Config\MenuConfig::menu() to merge the default menu with your menu. In the table you can define left key or tasks to display in menu left or in the menu task.
menu_left_collapsed | Set to tru to keep close the menu left. By default it set to false and the menu is open.
state | List of state available, with the color, the logo and the name.

### Menu

I use a function to easily merge the default component with the component of the application.

By default you can find on the menu left:

1.  Email
    1. List of email
    2. Add email
2.  User
    1. List of user
    2. Add user
3.  Role
    1. List of role
    2. Add role
4.  Permission
    1. Associate permission
    2. List of service
    3. Add service
    4. Synchronize all services
5.  Language
    1. List of language
    2. Add language

By default you can find on the menu task:

1.  Generate a new component
2.  Synchronize all services

To add a new item it's easy

```php
        'menu' => \Distilleries\Security\Config\MenuConfig::menu([
                'left' => [
                    [
                        'icon'    => 'send',
                        'action'  => 'Admin\ContactController@getIndex',
                        'libelle' => 'Contact',
                        'submenu' => [
                            [
                                'icon'    => 'th-list',
                                'libelle' => 'List of Contact',
                                'action'  => 'Admin\ContactController@getIndex',
                            ],
                            [
                                'icon'    => 'pencil',
                                'libelle' => 'Add Contact',
                                'action'  => 'Admin\ContactController@getEdit',
                            ]
                        ]
                    ],
                ],
    
                'tasks' => [
                    [
                        'icon'    => 'console',
                        'action'  => 'Admin\TestController@getIndex',
                        'libelle' => 'Test',
    
                    ],
                ]
            ], 'beginning'),
```

Option | Description
------ | -----------
icon | Name of the icon class [http://getbootstrap.com/components/#glyphicons](http://getbootstrap.com/components/#glyphicons)
action | Action call when you click ( use action helper to generate the url)
libelle | Translation of your menu item
submenu | If you want add sub-item you can add an array with the same options

The method `\Distilleries\Security\Config\MenuConfig::menu` tak two parameters

1. An array with the content of the meny `['left'=>[],'tasks'=>[]]` 
2. The second one is a string `beginning` or `end` to define the direction of the merge.

Example of menu left:

![menu_left](http://distilleri.es/markdown/Security/_images/menu_left.png)

Example of menu task:

![tasks](http://distilleri.es/markdown/Security/_images/tasks.png)

### State

A state is a part of your controller where you define a list of actions.
By default I implemented four states:

1. Datatable
2. Order
3. Export
4. Import
5. Form

To display the menu of state I provide a class for the interface `Distilleries\Security\Contracts\StateDisplayerContract`.
 
```php
 	$this->app->singleton('Distilleries\Security\Contracts\StateDisplayerContract', function ($app)
    {
        return new StateDisplayer($app['view'],$app['config']);
    });
```

This class check the interface use on your controller and with the config `exependable::state` display the logo and the name of the state.
If you want change the state display, just provide a new class for the contract `Distilleries\Security\Contracts\StateDisplayerContract`.
 
To display all the element I use a layout manager. you can override it to display what you want.
```php
 	  $this->app->singleton('Distilleries\Security\Contracts\LayoutManagerContract', function ($app)
    {
        return new LayoutManager($app['config']->get('Security'), $app['view'], $app['files'], app('Distilleries\Security\Contracts\StateDisplayerContract'));
    });
```

#### 1. Datatable

![datatable](http://distilleri.es/markdown/Security/_images/states.png)

A datatable state it's use to display a list of content with filter if you need it.
To use it you have to implement the interface `Distilleries\DatatableBuilder\Contracts\DatatableStateContract`.

```php
  public function getIndexDatatable();
  public function getDatatable();
```

* `getIndexDatatable` it's form initilize the datatable.
* `getDatatable` it's for get the data in json.

You can use the trait :

```php
use \Distilleries\Security\States\DatatableStateTrait;
```

On this trait you have a generic implementation to display the datatable and the data.
This trait need to use two attributes of your controller:

1. `$datatable`, it's an instance of `EloquentDatatable` (come from [DatatableBuilder](https://github.com/Distilleries/DatatableBuilder)).
2. `model`, it's and instance of `Model` (come from laravel).

Inject them on your constructor:

```php
    public function __construct(\Address $model, AddressDatatable $datatable)
    {
        $this->datatable  = $datatable;
        $this->model      = $model;
    }
```
    
#### 2. Order   
Add basic order feature to a component .
## Handle Controller
- must implements `\Distilleries\Security\Contracts\OrderStateContract`
- methods are implemented in `\Distilleries\Security\States\OrderStateTrait`
## Handle Model
- must implements `\Distilleries\Security\Contracts\OrderContract`
### Methods
- `orderLabel()` must return a string displayed in order page (by using model attributes)
- `orderFieldName()` must return the name of the field where the model persist the order


#### 3. Export

![export](http://distilleri.es/markdown/Security/_images/export.png)

An export state it's to export the data from your model between two dates.
To use it you have to implement the interface `Distilleries\Security\Contracts\ExportStateContract`.

```php
     public function getExport();
     public function postExport();
```

* `getExport` it's to display the form to select the dates and the type of export.
* `postExport` proceed the export and return the file.

You can use the trait :

```php
use \Distilleries\Security\States\ExportStateTrait;
```

On this trait you have a generic implementation to export your data.
This trait need to use on attribute of your controller:

1. `model`, it's and instance of `Eloquant` (come from laravel).

Inject them on your constructor:

```php
    public function __construct(\Address $model)
    {
        $this->model      = $model;
    }
```

You can change the class provide to export the data. Just add those methods on your service provider and change the class instantiated.

```php
    $this->app->singleton('Distilleries\Security\Contracts\CsvExporterContract', function ($app)
    {
        return new CsvExporter;
    });
    $this->app->singleton('Distilleries\Security\Contracts\ExcelExporterContract', function ($app)
    {
        return new ExcelExporter;
    });
    $this->app->singleton('Distilleries\Security\Contracts\PdfExporterContract', function ($app)
    {
        return new PdfExporter;
    });
```

#### 4. Import

![import](http://distilleri.es/markdown/Security/_images/import.png)

An import state it's to import the data from a file to your model.
To use it you have to implement the interface `Distilleries\Security\Contracts\ImportStateContract`.

```php
     public function getImport();
     public function postImport();
```

* `getImport` it's to display the form give the file.
* `postImport` proceed the import and return back.

You can use the trait :

```php
use \Distilleries\Security\States\ImportStateTrait;
```

On this trait you have a generic implementation to export your data.
This trait need to use on attribute of your controller:

1. `model`, it's and instance of `Eloquant` (come from laravel).

Inject them on your constructor:

```php
    public function __construct(\Address $model)
    {
        $this->model      = $model;
    }
```

You can change the class provide to import the data. Just add those methods on your service provider and change the class instantiated.

```php
    $this->app->singleton('CsvImporterContract', function ($app)
    {
        return new CsvImporter;
    });
    
    $this->app->singleton('XlsImporterContract', function ($app)
    {
        return new XlsImporter;
    });
    
    $this->app->singleton('XlsxImporterContract', function ($app)
    {
        return new XlsImporter;
    });
```

#### 5. Form

![form](http://distilleri.es/markdown/Security/_images/form.png)

The form state give you a part to add or edit an element and a part to view the element without edit.

To use it you have to implement the interface `Distilleries\FormBuilder\Contracts\FormStateContract`.

```php
    public function getEdit($id);
    public function postEdit();
    public function getView($id);
```

* `getEdit` it's to display the form to edit or add new item.
* `postEdit` proceed the save or update.
* `getView` Display the form in not editable.

You can use the trait :

```php
use \Distilleries\Security\States\FormStateTrait;
```

On this trait you have a generic implementation to display form, save and display view.
This trait need to use two attributes of your controller:

1. `model`, it's and instance of `Eloquant` (come from laravel).
1. `form`, it's and instance of `Form` (come from [FormBuilder](https://github.com/Distilleries/FormBuilder)).

Inject them on your constructor:

```php
     public function __construct(\Address $model, AddressForm $form)
    {
        $this->form      = $form;
        $this->model     = $model;
    }
```

## Component

A component is just a composition of controller, form, datatable, model.
To create a new component you can go in `/admin/component/edit` and fill the form, or use the command line:

```ssh
php artisan Security:component.make app/controllers/Admin/TestController
```
You can check the options with the help.

In the backend you have all this options:

Field | Description
----- | -----------
Name | The name use to generate the controllers and other classes (ex: Address, AddressController, AddressForm, AddressDatatable).
State | The state you want use on your controller
Model | The model inject on your controller
Columns | List of columns display on the datatable
Fields | The field you want in your form (name:type ex: id:hidden, libelle:text...)

To know all the types of fields you can [have look the documentation](https://github.com/Distilleries/FormBuilder#list-of-fields).

![component](http://distilleri.es/markdown/Security/_images/component.png)

### Admin BaseComponent

By default if you check all the state that generate a controller inheritance from `Distilleries\Security\Http\Controllers\Admin\Base\BaseComponent`.
This controller implement all the states interfaces.

```php
    use Distilleries\DatatableBuilder\Contracts\DatatableStateContract;
    use Distilleries\Security\Contracts\ExportStateContract;
    use Distilleries\Security\Contracts\ImportStateContract;
    use Distilleries\Security\States\DatatableStateTrait;
    use Distilleries\Security\States\ExportStateTrait;
    use Distilleries\Security\States\FormStateTrait;
    use Distilleries\Security\States\ImportStateTrait;
    use Distilleries\FormBuilder\Contracts\FormStateContract;
    
    class BaseComponent extends ModelBaseController implements FormStateContract, DatatableStateContract, ExportStateContract, ImportStateContract {
    
        use FormStateTrait;
        use ExportStateTrait;
        use DatatableStateTrait;
        use ImportStateTrait;
    
        // ------------------------------------------------------------------------------------------------
        // ------------------------------------------------------------------------------------------------
        // ------------------------------------------------------------------------------------------------
    
        public function getIndex()
        {
            return $this->getIndexDatatable();
        }
    }
```

### Admin ModelBaseController

If you don't want use all the state and you use a model just extend `Distilleries\Security\Http\Controllers\Admin\Base\ModelBaseController`.

Example:

```php
    use Distilleries\Security\Contracts\LayoutManagerContract;
    use Distilleries\Security\Models\BaseModel;
    use Illuminate\Http\Request;
    
    class ModelBaseController extends BaseController {
    
        /**
         * @var \Distilleries\Security\Models\BaseModel $model
         * Injected by the constructor
         */
        protected $model;
    
        // ------------------------------------------------------------------------------------------------
    
        public function __construct(BaseModel $model, LayoutManagerContract $layoutManager)
        {
            parent::__construct($layoutManager);
            $this->model = $model;
        }
	
        // ------------------------------------------------------------------------------------------------
        // ------------------------------------------------------------------------------------------------
        // ------------------------------------------------------------------------------------------------

        public function putDestroy(Request $request)
        {
            $validation = \Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validation->fails())
            {
                return redirect()->back()->withErrors($validation)->withInput($request->all());
            }
    
            $data = $this->model->find($request->get('id'));
            $data->delete();
    
            return redirect()->to(action('\\'.get_class($this) . '@getIndex'));
        }
    }
```

### Admin BaseController

If you don't want use all the state and you don't use a model just extend `Distilleries\Security\Http\Controllers\Admin\Base\BaseController`.
You just have to inject the `LayoutManagerContract`

Example:

```php
    use Distilleries\Security\Contracts\LayoutManagerContract;
    use Distilleries\Security\Http\Controllers\Controller;

    class BaseController extends Controller {
 
        protected $layoutManager;
	
        protected $layout = 'Security::admin.layout.default';
    
        // ------------------------------------------------------------------------------------------------
    
        public function __construct(LayoutManagerContract $layoutManager)
        {
            $this->layoutManager = $layoutManager;
            $this->setupLayout();
        }
    
        // ------------------------------------------------------------------------------------------------
        // ------------------------------------------------------------------------------------------------
        // ------------------------------------------------------------------------------------------------
    
        protected function setupLayout()
        {
            $this->layoutManager->setupLayout($this->layout);
            $this->setupStateProvider();
            $this->initStaticPart();
    
    
        }
    
        // ------------------------------------------------------------------------------------------------
    
        protected function setupStateProvider()
        {
            $interfaces = class_implements($this);
            $this->layoutManager->initInterfaces($interfaces, get_class($this));
    
        }
    
        // ------------------------------------------------------------------------------------------------
    
        protected function initStaticPart()
        {
            $this->layoutManager->initStaticPart(function ($layoutManager)
            {
    
                $menu_top  = $layoutManager->getView()->make('Security::admin.menu.top');
                $menu_left = $layoutManager->getView()->make('Security::admin.menu.left');
    
    
                $layoutManager->add([
                    'state.menu' => $layoutManager->getState()->getRenderStateMenu(),
                    'menu_top'   => $menu_top,
                    'menu_left'  => $menu_left
                ]);
            });
        }
    }
```

## Model

By default you can extend `\Distilleries\Security\Models\BaseModel`, this one extend `\Illuminate\Database\Eloquent\Model`.
On it you have some method you can use:

```php
    public static function getChoice();
    public function scopeSearch($query, $searchQuery);
    public function getAllColumnsNames();
    public function scopeBetweenCreate($query, $start, $end);
    public function scopeBetweenupdate($query, $start, $end);
```

Method | Detail
------ | ------
getChoice   | Return a table with in key the id and the value the libelle
scopeSearch | Query scope to search in all columns
getAllColumnsNames | Get all the columns of your table
scopeBetweenCreate | Query scope to get all the element between to date by the field created_at
scopeBetweenupdate | Query scope to get all the element between to date by the field created_at

## Global scope
I provide some global scope usable on the model.

### Status

If you want display an element only if your are connected use this scope.
The model check if the user is not connected and if the status equal online (1).

To use it add the trait on your model `use \Distilleries\Security\Models\StatusTrait;`

## Permissions

The system of permission is base on the public method of all your controller.
To generate the list of all services use the `Synchronize all services` (`/admin/service/synchronize`).
That use all the controller and get the public actions.

If you go on `Associate Permission` you have the list of controller with all methods:

![services](http://distilleri.es/markdown/Security/_images/services.png)

On this page you can allow a role to the method.
By default Security use `Distilleries\PermissionUtil` package and add the good middleware in his kernel.
You don't have to configure something.
If the role is not allowed the application dispatch an error 403:

```php
    if(!PermissionUtil::hasAccess('Controller@action')){
        App::abort(403, Lang::get('Security::errors.unthorized'));
    }
```

## Views

To override the view publish them with command line: 

```ssh
php artisan vendor:publish --provider="Distilleries\Security\SecurityServiceProvider"  --tag="views"
```

## Assets (CSS and Javascript)

All the assets are one the folder `resources/assets`.

### Sass

To use the sass file just add bootstrap and  `application.admin.scss` on your admin file scss.
If you check the repo [Xyz](https://github.com/Distilleries/Xyz/tree/master/resources/assets) you have a folder assets.
I use the same structure.

```scss
   //
   // Third-parties
   //
   
   @import "../../../../node_modules/bootstrap-sass/assets/stylesheets/bootstrap";
   @import "../../../../node_modules/font-awesome/scss/font-awesome";
   
   //
   // Security
   //
   
   @import "../../../../vendor/distilleries/Security/src/resources/assets/backend/sass/application.admin";
   @import "../../../../vendor/distilleries/Security/src/resources/assets/backend/sass/admin/layout/themes/grey";
```

### Images

The images are copy by mix script when they are found in sass file

### Javascript

The javascript is compiled by the mix

### Composer

I update my composer json to add the npm install and gulp generation when I update my libraries.

```json
    "post-update-cmd": [
      "php artisan clear-compiled",
      "php artisan optimize",
      "php artisan down",
      "npm install",
      "php artisan migrate --force",
      "npm run production",
      "php artisan up"
    ],
```
    
## Create a new backend module

1. Generate your migration.
2. Generate your model.
3. Generate you component.
4. Add your controller in the routes file.

## Case studies

Try to create a blog post component. I use a fresh install of [Xyz](https://github.com/Distilleries/Xyz)

### 1. Generate your migration

```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('posts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('libelle');
			$table->text('content')->nullable();
			$table->tinyInteger('status');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('posts');
	}

}

```

```ssh
php artisan migrate
```

### 2. Generate your model

```php
    use Distilleries\Security\Models\BaseModel;
    
    class Post extends BaseModel {
        
        use \Distilleries\Security\Models\StatusTrait;
    
        protected $fillable = [
            'id',
            'libelle',
            'content',
            'status',
        ];
    }
```

### 3 Generate you component

I use the backend generator `/admin/component/edit`.

![studies](http://distilleri.es/markdown/Security/_images/studies.png)


Datatable:

```php
<?php namespace App\Datatables;

use Distilleries\DatatableBuilder\EloquentDatatable;

class PostDatatable extends EloquentDatatable
{
    public function build()
    {
        $this
            ->add('id',null,trans('datatable.id'))
            ->add('libelle',null,trans('datatable.libelle'));

        $this->addDefaultAction();

    }
}

```

Form:

This file is generated:

```php
<?php namespace App\Forms;

use Distilleries\FormBuilder\FormValidator;

class PostForm extends FormValidator
{
    public static $rules        = [];
    public static $rules_update = null;

    public function buildForm()
    {
        $this
            ->add('id', 'hidden')
            ->add('libelle', 'text')
            ->add('content', 'tinymce')
            ->add('status', 'choice');

         $this->addDefaultActions();
    }
}
```

You have to update it for give a value for the choice and give the rules for the validation:

```php
<?php namespace App\Forms;

use Distilleries\Security\Helpers\StaticLabel;
use Distilleries\FormBuilder\FormValidator;

class PostForm extends FormValidator
{
    public static $rules = [
        'libelle' => 'required',
        'status'  => 'required|integer'
    ];
    public static $rules_update = null;

    public function buildForm()
    {
        $this
            ->add('id', 'hidden')
            ->add('libelle', 'text')
            ->add('status', 'choice', [
                'choices'     => StaticLabel::status(),
                'empty_value' => '-',
                'validation'  => 'required',
                'label'       => 'Status'
            ]);

         $this->addDefaultActions();
    }
}
```

Controller:

```php
<?php namespace App\Http\Controllers\Admin;

use Distilleries\Security\Contracts\LayoutManagerContract;
use Distilleries\Security\Http\Controllers\Admin\Base\BaseComponent;
use App\Forms\PostForm;
use App\Datatables\PostDatatable;

class PostController extends BaseComponent {

    // ------------------------------------------------------------------------------------------------
    // ------------------------------------------------------------------------------------------------
    // ------------------------------------------------------------------------------------------------

    public function __construct(PostDatatable $datatable, PostForm $form, \App\Post $model, LayoutManagerContract $layoutManager)
    {
       parent::__construct($model, $layoutManager);
       
       $this->datatable = $datatable;
       $this->form      = $form;
    }
}
```


### 4 Add your controller in the routes file
I add ` Route::controller('post', 'Admin\PostController');` on the route file:

```php
    <?php
    
    use \Route;
    
    Route::get('/', 'HomeController@index');
    
    Route::group(array('middleware' => 'auth'), function ()
    {
    
        Route::group(array('before' => 'permission', 'prefix' => config('Security::admin_base_uri')), function ()
        {
            Route::controller('post', 'Admin\PostController');
        });
    });
```


### 5 Add to the menu

On `config/Security.php` id add the Post entry:

```php
        'menu'  => \Distilleries\Security\Config\MenuConfig::menu([
            'left' => [
                [
                    'icon'    => 'pushpin',
                    'action'  => 'Admin\PostController@getIndex',
                    'libelle' => 'Post',
                    'submenu' => [
                        [
                            'icon'    => 'th-list',
                            'libelle' => 'List of Post',
                            'action'  => 'Admin\PostController@getIndex',
                        ],
                        [
                            'icon'    => 'pencil',
                            'libelle' => 'Add Post',
                            'action'  => 'Admin\PostController@getEdit',
                        ]
                    ]
                ],
            ]
        ], 'beginning'),
```
