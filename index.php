<?php


$basePath = (__DIR__) . "";
require_once($basePath . "/app/autoloader.php");


$router = new Router(
    templatePath: $basePath . '/templates',
    staticPath: $basePath . '/static',
    cachePath: null,
    autoescape: false,
    defaultHtml: true,
);

$router->addGlobal('app_name', 'Seku Market');
$router->addGlobal('current_year', date('Y'));

$router->addFilter('stars', fn($value) => str_repeat('⭐', (int) $value));
$router->addFilter('currency', fn($amount, $symbol = 'UGX: ') => $symbol . number_format($amount, 2));


$router->setErrorHandler(function ($class, $args) {
    return $class->render('errors/error.html', $args->getArray());
});

$router->get('/', function ($class, $args) {
    return $class->render('authorize.html', []);
    // return $class->getSupportedHttpMethods();

    // return $class->formatMessage(new Exception("OK", 200), 0, $class->getSupportedHttpMethods());

    return $class->formatMessage(new Exception("OK", 200), 0, array(
        "url_for" => array(
            'static' => url_for('static', filename: 'css/app.css', id: 20),
            'router' => url_for('user_profile', id: 25, _scheme: 'http'),
            'user Posts' => url_for('user_profile', id: 25, tab: 'posts'),
            'external' => url_for('user_profile', id: 25, name: "fahad", _external: true),
            'scheme' => url_for('user_profile', id: 25, name: "fahad", _scheme: 'https'),
            'anchor' => url_for('user_profile', id: 25, name: "fahad", _anchor: 'comments'),
        )
    ));
}, false);



$router->get('/user_profile/{id:int}', function ($class, $args) {
    return $class->render('authorize.html', []);


    // return $class->render('sample/index.html', []);
}, false, 'User profile', array('username' => 'string', 'password' => 'string'));


