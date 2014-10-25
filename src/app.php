<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Application settings.
$app = new Silex\Application();
$app['debug'] = true;
$app['api.version'] = 'v1';
$app['api.endpoint'] = '/api';

// Database settings.
$db_config = require __DIR__.'/database.php';
$db_default_conn = $db_config['environments']['default_database'];

// Register DBAL.
$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'dbname' => $db_config['environments'][$db_default_conn]['name'],
        'user' => $db_config['environments'][$db_default_conn]['user'],
        'password' => $db_config['environments'][$db_default_conn]['pass'],
        'host' => $db_config['environments'][$db_default_conn]['host'],
        'driver' => 'pdo_'.$db_config['environments'][$db_default_conn]['adapter'],
        'charset' => 'utf8',
    ]
]);

// Users routers.
$users = $app['controllers_factory'];
$users->post('/', function (Request $request) use ($app) {
    $name = $request->get('name');

    if (empty($name)) {
        return new Response('', 400);
    }

    $app['db']->insert('users', ['name' => $name]);

    return new Response('', 201);
});
$users->delete('/{id}', function ($id) use ($app) {
    if ($app['db']->delete('users', array('id' => $id))) {
        return '';
    }

    return new Response('', 404);
})->assert('id', '\d+');

// Groups routers.
$groups = $app['controllers_factory'];
$groups->post('/', function (Request $request) use ($app) {
    $name = $request->get('name');

    if (empty($name)) {
        return new Response('', 400);
    }

    $app['db']->insert('groups', ['name' => $name]);

    return new Response('', 201);
});
$groups->delete('/{id}', function ($id) use ($app) {
    if ($app['db']->delete('groups', array('id' => $id))) {
        return '';
    }

    return new Response('', 404);
})->assert('id', '\d+');

// Mount routers on API url.
$app->mount(
    $app['api.endpoint'].'/'.$app['api.version'].'/users',
    $users
);

$app->mount(
    $app['api.endpoint'].'/'.$app['api.version'].'/groups',
    $groups
);

return $app;
