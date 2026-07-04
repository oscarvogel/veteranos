<?php
$base = __DIR__ . '/../app/';

$folders = [
    'lib',
    'model',
];

foreach($folders as $f)
{
    foreach (glob($base . "$f/*.php") as $k => $filename)
    {
        require_once $filename;
    }
}

