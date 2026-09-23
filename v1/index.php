<?php

$basePath = dirname(__DIR__) . "";
require_once($basePath . "/app/autoloader.php");
require_once($basePath . "/v1/Documentation.php");

$docs = new Documentation();
$router = new Router(
   templatePath: $basePath . '/templates',
   staticPath: $basePath . '/static',
   cachePath: null,
   autoescape: false,
   // defaultHtml: true,
);

$router->autoModelRegister();

// $router->addGlobal('app_name', 'ERP Version One');
$router->addGlobal('api_name', 'ERP Version One');
$router->addGlobal('api_version', 'Version 1.0.0');

$router->addThrowable("unsupported", new Exception("HTTP method [" . $_SERVER['REQUEST_METHOD'] . "] is not supported for this endpoint", 405));




// // Home page – HTML
// $router->post('/docs', function ($class, $args) {
//    $model = $class->getModel('AuthorizationModel');

//    return $model->signin($args->getValue('username'), $args->getValue('password'));
// }, false);



// Home page – HTML
$router->get('/', function ($class, $args) {
   $appName = strtoupper($class->getGlobal('api_name'));
   $docPath = url_for('/docs', _scheme: 'http');

   return $class->formatMessage(new Exception("Welcome to {$appName} API, please visit: [ {$docPath} ] for the documentations.", 200));
}, false);

// Home page – HTML
$router->get('/docs', function ($class, $args) {
   $model = $class->getModel('ApiModel');
   $grouped = $model->groupRoutesByPath($class->getSupportedHttpMethods(), array('/', '/docs'), true, );
   // return $class->formatMessage(new Exception("Apis"), 0, array('endpoints' => $grouped));
   $endpoints = $model->sortMethods(array('endpoints' => $grouped));
   return $class->render('api/api.html', array('endpoints' => $endpoints));
}, false);





$router->post('/signin', function ($class, $args) {
   $model = $class->getModel('UserAuth');
   return $model->signin($args->getValue('username'), $args->getValue('password'));
}, false, "Smaple Dex", $docs->getValue('post', 'signin'));


$router->any('academicyear', array('GET', 'POST', 'PATCH', 'DELETE'), function ($class, $args) {
   
}, $docs->getValue('any', 'academicyear'));


$router->any('sessions', array('GET', 'POST', 'PATCH', 'DELETE'), function ($class, $args) {
   $model = $class->getModel('SettingSession');
   $action = $args->getValue('action');
   if($action === 'SELECT') {
      return $model->findAll();
   }else if ($action === 'INSERT') {
      return $model->insert($args->getArray());
   }else if ($action === 'UPDATE') {
       return $model->update($args->getValue('idnum'), array(
         // 'idnum'=> $args->getValue('idnum'),
         'name'=> $args->getValue('name'),
         'active'=> strtoupper($args->getValue('active'))
         ));
   }else if ($action === 'DELETE') {
       return $model->delete($args->getValue('idnum'));
   }
   return $class->formatMessage(new Exception("Apis"), 0, array('data' => $args->getArray()));
}, $docs->getValue('any', 'sessions'));
