<?php

use function App\make_city_model;
use function App\make_user_model;
use function App\render;

require __DIR__ . '/../../src/autoload.php';

$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);

$search = ! empty($search) ? trim($search) : $search;

$errors = [];
if ($search === '') {
    $errors[] = 'Ввете запрос в строку поиска';
}
if ($search === false) {
    $errors[] = 'Введенные данные не валидны';
}

if ($search !== null && $search !== '') {
    $userModel = make_user_model();
    $users = $userModel->getUsersBySearch($search);

    $cityModel = make_city_model();
    $cities = $cityModel->getAllCity();

    echo render('search', ['users' => $users, 'cities' => $cities, 'errors' => $errors]);
    return;
}

echo render('search', ['errors' => $errors]);

