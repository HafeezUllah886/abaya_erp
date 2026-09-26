<?php

// script to scaffold issue and receive voucher CRUD

$paths = [
    'resources/views/manufacturing/issue/index.blade.php',
    'resources/views/manufacturing/issue/edit.blade.php',
    'resources/views/manufacturing/issue/show.blade.php',
    'resources/views/manufacturing/receive/index.blade.php',
    'resources/views/manufacturing/receive/edit.blade.php',
    'resources/views/manufacturing/receive/show.blade.php',
];

foreach($paths as $path) {
    if (!file_exists(dirname($path))) {
        mkdir(dirname($path), 0777, true);
    }
}
