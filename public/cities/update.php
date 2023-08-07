<?php

use function App\abort;
use function App\make_city_model;
use function App\redirect;
use function App\render;

require __DIR__ . '/../../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === false) {
        abort(404);
    }

    $cityModel = make_city_model();
    $city = $cityModel->getCityById($id);
    if ($city === null) {
        abort(404);
    }

    echo render('city_update', ['city' => $city, 'errors' => []]);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parameters = filter_input_array(INPUT_POST, [
        'name' => [
            'filter' => FILTER_SANITIZE_STRING,
        ],
        'index' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 1,
            ],
        ],
        'id' => [
            'filter' => FILTER_VALIDATE_INT,
        ],
    ]);
    if ($parameters === null) {
        abort(422);
    }
    if (empty($parameters['id'])) {
        abort(422);
    }
    $cityModel = make_city_model();
    $city = $cityModel->getCityById($parameters['id']);
    if ($city === null) {
        abort(404);
    }

    $errors = [];
    if (empty($parameters['name']) || trim($parameters['name']) === '') {
        $errors[] = 'Заполните название города';
        $parameters['name'] = '';
    }
    if (empty($parameters['index'])) {
        $errors[] = 'Заполните индекс сортировки';
        $parameters['index'] = '';
    }
    if (count($errors) > 0) {
        echo render('city_update', ['city' => $parameters, 'errors' => $errors]);
        return;
    }

    $cityModel->updateCity($parameters['id'], $parameters['name'], $parameters['index']);

    redirect('/cities/index.php', 302);
}

abort(405);
