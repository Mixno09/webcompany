<?php

require __DIR__ . '/../../src/autoload.php';

use function App\abort;
use function App\make_city_model;
use function App\redirect;

$id = isset($_POST['id']) ? $_POST['id'] : null;

if ($id !== null) {
    $city_model = make_city_model();
    if ($city_model->deleteCityById($id) === 0) {
        abort(400);
    }

    redirect('/cities/index.php', 302);
} else {
    abort(405);
}

