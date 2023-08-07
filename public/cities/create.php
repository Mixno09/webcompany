<?php

require __DIR__ . '/../../src/autoload.php';

use function App\abort;
use function App\form_data_city_create;
use function App\make_city_model;
use function App\redirect;
use function App\render;
use function App\validate_city_parameters;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo render('city_create');
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parameters = form_data_city_create();
    $parameters = validate_city_parameters($parameters);

    if ($parameters['name'] !== '' && $parameters['index'] !== false) {
        $city_model = make_city_model();
        $city_model->insertCity($parameters['name'], $parameters['index']);
    } else {
        echo render('city_create');
    }

    redirect('/cities/index.php', 303);
}

abort(405);
