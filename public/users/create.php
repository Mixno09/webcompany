<?php

use function App\abort;
use function App\copy_file;
use function App\make_city_model;
use function App\make_user_model;
use function App\redirect;
use function App\render;

require __DIR__ . '/../../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $cityModel = make_city_model();
    $cities = $cityModel->getAllCity();
    echo render('user_create', ['cities' => $cities]);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    if (empty($name) || trim($name) === '') {
        $errors[] = 'Заполните имя';
        $name = '';
    }

    $surName = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
    if (empty($surName) || trim($surName) === '') {
        $errors[] = 'Заполните фамилию';
        $surName = '';
    }

    $cityId = filter_input(INPUT_POST, 'cityId', FILTER_CALLBACK, [
        'options' => function ($cityId) {
            $cityId = filter_var($cityId, FILTER_VALIDATE_INT);
            if (! is_int($cityId)) {
                return $cityId;
            }

            $cityModel = make_city_model();
            $city = $cityModel->getCityById($cityId);
            if ($city === null) {
                return false;
            }

            return $cityId;
        }
    ]);
    if (! is_int($cityId)) {
        $errors[] = 'Заполните город';
        $cityId = null;
    }

    $image = isset($_FILES['uploadfile']) ? $_FILES['uploadfile'] : null;

    if ($image === null) {
        abort(400);
    }

    if (! is_array($image)) {
        abort(400);
    }

    if (! array_key_exists('error', $image)) {
        abort(400);
    }

    if ($image['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Выберите картинку';
    }

    if ($image['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Не валидная картинка';
    }

    if (! is_uploaded_file($image['tmp_name'])) {
        $errors[] = 'Файл не выбран';
    }

    if ($image['tmp_name'] !== '') {
        $mime = mime_content_type($image['tmp_name']);
        $validMimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (! in_array($mime, $validMimes, true)) {
            $errors[] = 'Не верный тип файла';
        }
    }

    if (count($errors) > 0) {
        $cityModel = make_city_model();
        $cities = $cityModel->getAllCity();

        $data = [
            'name' => $name,
            'surname' => $surName,
            'cityId' => $cityId,
        ];

        echo render('user_create', [ 'data' => $data, 'cities' => $cities, 'errors' => $errors]);
        return;
    }

    $fileName = copy_file($image['tmp_name'], IMAGE_DOCUMENT_ROOT);

    $userModel = make_user_model();
    $userModel->insertUser($name, $surName, $cityId, $fileName);

    redirect('/users/index.php', 302);
}

abort(405);
