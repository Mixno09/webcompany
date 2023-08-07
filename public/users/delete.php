<?php

use function App\abort;
use function App\make_user_model;
use function App\redirect;

require __DIR__ . '/../../src/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    abort(405);
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id === false) {
    $id = null;
}

if ($id === null) {
    abort(400);
}

$userModel = make_user_model();
if ($userModel->deleteUserById($id) === 0) {
    abort(400);
}

redirect('/users/index.php', 302);
