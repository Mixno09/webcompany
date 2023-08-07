<?php

namespace App;

use PDO;

function render(string $template, array $data = [], ?string $layout = 'layout'): string
{
    $content = (function (string $template, array $data): string {
        extract($data);
        ob_start();
        require __DIR__ . "/../templates/{$template}.phtml";
        return ob_get_clean();
    })($template, $data);

    if ($layout === null) {
        return $content;
    }

    ob_start();
    require __DIR__ . "/../templates/{$layout}.phtml";
    return ob_get_clean();
}

function make_pdo(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $params = require CONFIG_DIR . '/database.php';
        $pdo = new PDO(
            "mysql:host={$params['host']};port={$params['port']};dbname={$params['dbname']}",
            $params['user'],
            $params['password']
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}

function make_city_model(): CityModel
{
    static $cityModel = null;

    if ($cityModel === null) {
        $pdo = make_pdo();
        $cityModel = new CityModel($pdo);
    }

    return $cityModel;
}

function make_user_model(): UserModel
{
    static $userModel;

    if ($userModel === null) {
        $pdo = make_pdo();
        $userModel = new UserModel($pdo);
    }

    return $userModel;
}

function render_counter(string $name): string
{
    $count = isset($_COOKIE[$name]) ? (int)$_COOKIE[$name] : 0;
    $count++;
    setcookie($name, $count, time() + 60 * 60 * 24 * 30, '/');

    $stat = isset($_COOKIE['count_total']) ? (int)$_COOKIE['count_total'] : 0;
    $stat++;
    setcookie('count_total', $stat, time() + 60 * 60 * 24 * 30, '/');

    return render('counter', ['count' => $count, 'stat' => $stat], null);
}

function abort(int $code): void
{
    http_response_code($code);
    echo render('abort', ['code' => $code], null);
    die();
}

function form_data_city_create(): array
{
    $name = isset($_POST['instextcity']) ? $_POST['instextcity'] : null;
    $index = isset($_POST['instextrangir']) ? $_POST['instextrangir'] : null;

    $parameters['name'] = $name;
    $parameters['index'] = $index;

    return $parameters;
}

function validate_city_parameters(array $parameters): array
{
    $options = [
        'name' => [
            'filter' => FILTER_SANITIZE_STRING,
        ],
        'index' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 1,
            ],
        ],
    ];

    return filter_var_array($parameters, $options);
}

function redirect(string $url, int $statusCode = 302)
{
    header('Location: ' . $url, true, $statusCode);
    die();
}

function copy_file(string $file, string $dir): string
{
    $mime = mime_content_type($file); // Определяем MIME-тип содержимого файла, где $file путь к проверяемому файлу
    $ext = ext_by_mime($mime);

    do {
        $name = uniqid() . $ext; // Генерируем уникальный ID (имя для картинки)
        $dist = $dir . $name;
    } while (file_exists($dist)); // Проверяем существование указанного файла в нашей директории product
    copy($file, $dist);

    return $name;
}

/**
 * Преобразуем MIME-тип файла (например image/jpeg) в файл с расширением например .jpg
 */
function ext_by_mime(string $mime): string
{
    $map = [
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'image/gif' => '.gif',
    ];
    $ext = '';
    if (array_key_exists($mime, $map)) {
        $ext = $map[$mime];
    }
    return $ext;
}

function url(string $baseUrl, string $fileName): string
{
    $fileName = rawurlencode($fileName);
    $url = $baseUrl . $fileName;

    return $url;
}
