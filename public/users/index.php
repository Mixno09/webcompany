<?php

use function App\make_city_model;
use function App\make_user_model;
use function App\render;

require __DIR__ . '/../../src/autoload.php';

$cityId = filter_input(INPUT_GET, 'cityId', FILTER_VALIDATE_INT);
$sort = filter_input(INPUT_GET, 'sort', FILTER_VALIDATE_INT);
$parameterSort = filter_input_array(INPUT_GET, [
    'order_by' => [
        'filter' => FILTER_SANITIZE_STRING,
    ],
    'order' => [
        'filter' => FILTER_SANITIZE_STRING,
    ],
]);

if ($cityId === false) {
    $cityId = null;
}
if ($sort === false) {
    $sort = null;
}

// рендер формы сортировки
if ($sort === 1) {
    $userModel = make_user_model();
    $users = $userModel->getAllUser();

    $cityModel = make_city_model();
    $cities = $cityModel->getAllCity();
    echo render('user_index', ['users' => $users, 'cities' => $cities, 'cityId' => $cityId, 'sort' => $sort]);
    return;
}

//сортировка по городам
if ($cityId !== null) {
    $userModel = make_user_model();
    $users = $userModel->getUsersByCityId($cityId);

    $cityModel = make_city_model();
    $cities = $cityModel->getAllCity();

    echo render('user_index', ['users' => $users, 'cities' => $cities, 'cityId' => $cityId, 'sort' => $sort]);
    return;
}

//рендер отсортированных пользователей
if (! empty($parameterSort['order_by']) && ! empty($parameterSort['order'])) {
    $userModel = make_user_model();
    $users = $userModel->getAllUser($parameterSort['order_by'], $parameterSort['order']);

    $cityModel = make_city_model();
    $cities = $cityModel->getAllCity();
    echo render('user_index', ['users' => $users, 'cities' => $cities, 'cityId' => $cityId, 'sort' => $sort]);
    return;
}

$userModel = make_user_model();
$users = $userModel->getAllUser();

$cityModel = make_city_model();
$cities = $cityModel->getAllCity();

echo render('user_index', ['users' => $users, 'cities' => $cities, 'cityId' => $cityId, 'sort' => $sort]);
