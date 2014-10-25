<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Application settings.
$app = new Silex\Application();
$app['debug'] = true;
$app['api.version'] = 'v1';
$app['api.endpoint'] = '/api';
$app['admin.user'] = 'admin';
$app['admin.pass'] = 'password123'; // ENCRYPT!

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

// HTTP basic auth.
$app->before(function (Request $request) use ($app) {
    if ($app['admin.user'] !== [$_SERVER['PHP_AUTH_USER']] &&
    $app['admin.pass'] !== $_SERVER['PHP_AUTH_PW']) {
        header('WWW-Authenticate: Basic realm=API');

        return new Response('', 401); // Not authorized.
    }
});

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
        return new Response('', 204);
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
    $has_users = $app['db']->fetchColumn(
        'SELECT COUNT(*) FROM user_groups WHERE group_id = ?',
        [$id]
    );

    if ($has_users) {
        return '';
    }

    if ($app['db']->delete('groups', array('id' => $id))) {
        return new Response('', 204);
    }

    // Group was not found.
    return new Response('', 404);
})->assert('id', '\d+');

// Users managements on groups.
$users->post('/{user_id}/add_group/{group_id}', function ($user_id, $group_id) use ($app) {
    $exists_ids = $app['db']->fetchColumn(
        'SELECT 1 FROM users INNER JOIN groups ON groups.id = ? WHERE users.id = ?',
        [$group_id, $user_id]
    );

    if (!$exists_ids) {
        return new Response('', 404);
    }

    // Check duplication.
    try {
        $app['db']->insert('user_groups', [
            'user_id' => $user_id,
            'group_id' => $group_id,
        ]);
    } catch (\Doctrine\DBAL\DBALException $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            return ''; // Return 200.
        }
    }

    return new Response('', 201);
})->assert('user_id', '\d+')->assert('group_id', '\d+');

$users->delete('/{user_id}/delete_group/{group_id}', function ($user_id, $group_id) use ($app) {
    if ($app['db']->delete('user_groups', ['user_id' => $user_id, 'group_id' => $group_id])) {
        return new Response('', 204);
    }

    return new Response('', 404);
})->assert('user_id', '\d+')->assert('group_id', '\d+');

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
