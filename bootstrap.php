<?php

require __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();
