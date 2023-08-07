<?php

require __DIR__ . '/../../src/autoload.php';

use function App\abort;
use function App\make_city_model;
use function App\render;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    abort(405);
}

$sort = filter_input(INPUT_GET, 'sort', FILTER_VALIDATE_INT);
$parameterSort = filter_input_array(INPUT_GET, [
    'order_by' => [
        'filter' => FILTER_SANITIZE_STRING,
    ],
    'order' => [
        'filter' => FILTER_SANITIZE_STRING,
    ],
]);

//рендер формы сортировки
if ($sort === 1) {
    $cityModel = make_city_model();
    $cities = $cityModel->getAllCity();
    echo render('cities_index', ['cities' => $cities, 'sort' => $sort]);

    return;
}

//рендер отсортированных городов
if (! empty($parameterSort['order_by']) && ! empty($parameterSort['order'])) {
    $cityModel = make_city_model();
    $cities = $cityModel->getAllCity($parameterSort['order_by'], $parameterSort['order']);

    echo render('cities_index', ['cities' => $cities, 'sort' => $sort]);
    return;
}

$cityModel = make_city_model();
$cities = $cityModel->getAllCity();
echo render('cities_index', ['cities' => $cities, 'sort' => $sort]);

