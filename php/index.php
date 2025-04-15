<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/controllers/DocentiController.php';
require __DIR__ . '/includes/Db.php';
require __DIR__ . '/controllers/ScuoleController.php';

$app = AppFactory::create();

$app->get('/scuole/{scuola_id}/docenti', "DocentiController:index");

$app->get('/scuole/{idScuola}/docenti/{idDocente}', "DocentiController:show");

$app->post('/scuole/{id_scuola}/docenti', "DocentiController:create");

$app->put('/scuole/{id}/docenti/{idDoc}', "DocentiController:update");

$app->delete('/scuole/{id}/docenti/{idDocen}', "DocentiController:destroy");

$app->get('/scuole', "ScuoleController:index");

$app->get('/scuole/{id:\d+}', "ScuoleController:show");

$app->get('/scuole/{nome}', "ScuoleController:search");

$app->post('/scuole', "ScuoleController:create");


$app->put('/scuole/{id}', "ScuoleController:update");


$app->delete('/scuole/{id}', "ScuoleController:destroy");

$app->get('/scuole/sortScuole/{column}[/{order}]', "ScuoleController:sort");

$app->get('/scuole/sortDocenti/{idScuol}/docenti/{column}[/{order}]', "DocentiController:sort");
 

$app->run();
