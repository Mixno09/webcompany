<?php

use function App\abort;
use function App\copy_file;
use function App\make_city_model;
use function App\make_user_model;
use function App\redirect;
use function App\render;

require __DIR__ . '/../../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (! is_int($id)) {
        abort(400);
    }

    $userModel = make_user_model();
    $user = $userModel->getUserById($id);

    if ($user === null) {
        abort(404);
    }

    $cityModel = make_city_model();
    $cities = $cityModel->getAllCity();

    echo render('user_update', ['user' => $user, 'cities' => $cities]);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (! is_int($id)) {
        abort(400);
    }

    $userModel = make_user_model();
    $user = $userModel->getUserById($id);
    if ($user === null) {
        abort(404);
    }

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

    $newImage = isset($_FILES['uploadfile']) ? $_FILES['uploadfile'] : null;

    if ($newImage === null) {
        abort(400);
    }

    if (! is_array($newImage)) {
        abort(400);
    }

    if (! array_key_exists('error', $newImage)) {
        abort(400);
    }

    if ($newImage['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Выберите картинку';
    }

    if ($newImage['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Не валидная картинка';
    }

    if (! is_uploaded_file($newImage['tmp_name'])) {
        $errors[] = 'Файл не выбран';
    }

    if ($newImage['tmp_name'] !== '') {
        $mime = mime_content_type($newImage['tmp_name']);
        $validMimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (! in_array($mime, $validMimes, true)) {
            $errors[] = 'Не верный тип файла';
        }
    }

    if (count($errors) > 0) {
        $cityModel = make_city_model();
        $cities = $cityModel->getAllCity();

        $data = [
            'id' => $user['id'],
            'name' => $name,
            'surname' => $surName,
            'cityid' => $cityId,
            'filename' => $user['filename'],
        ];
        echo render('user_update', ['user' => $data, 'cities' => $cities, 'errors' => $errors]);
        return;
    }

    $fileName = null;
    if ($newImage !== null) {
        $fileName = copy_file($newImage['tmp_name'], IMAGE_DOCUMENT_ROOT);
    }

    $userModel = make_user_model();
    $oldFilename = $user['filename'];
    $userModel->updateUser($id, $name, $surName, $cityId, $fileName);

    if (file_exists(__DIR__ . '/../' . PUBLIC_PATH . $oldFilename)) {
        unlink(__DIR__ . '/../' . PUBLIC_PATH . $oldFilename);
    }

    redirect('/users/index.php');
}
