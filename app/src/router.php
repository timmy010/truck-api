<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/orders') {
    // Вызовите контроллер для обработки заказов
    echo "Orders Controller";
} else {
    echo "404 Not Found";
}