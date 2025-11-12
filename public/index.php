<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$templatePath = __DIR__ . '/../templates/index.html';

if (file_exists($templatePath)) {
    echo file_get_contents($templatePath);
} else {
    http_response_code(500);
    echo '<h1>Errorrrr</h1>';
}
