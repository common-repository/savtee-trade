<?php

$config = [];

foreach (scandir(__DIR__ . '/components') as $file) {
    if (!in_array($file, ['.', '..'])) {
        $key = substr($file, 0, -4);
        $config[strtoupper($key)] = (include 'components/' . $file);
    }
}

return $config;