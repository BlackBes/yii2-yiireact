React integration for Yii2
===================
Yii2 extension for support a react-based frontend.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist blackbes/yii2-yiireact "*"
```

or add

```
"blackbes/yii2-yiireact": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, add this code to your web.php into `$config['modules']['gii']` :

```php
'generators' => [ //here
    'yiiReact' => [
        'class' => 'blackbes\yiireact\yiiReactCrud\Generator',
        'templates' => [
    'react' => 'blackbes/yiireact/yiiReactCrud/default',
        ]
    ]
],
```
If done correctly, you can now access React API CRUD Generator in Yii2 Gii

Usage instructions
----
First of all make sure that you have installed [react-yii2-essentials](https://github.com/BlackBes/react-yii2-essentials) extension on your React part of the project.

If it is installed then:

1) Create any Model using Gii Model generator
2) Make newly generated model extend `blackbes\yiireact\models\ParentModel`
3) Use installed React API CRUD Generator for this model. It will generate a Model Controller in `/controllers/api/`
and all pages with forms for React in `/views/api/<Model name>/`.
4) Create `src/containers` folder in the root directory of your React project
5) Copy the `/views/api/<Model name>/` from Yii2 and paste it into `src/containers` directory of your React
project
6) Use `_routes.js` to add routes and import generated files
