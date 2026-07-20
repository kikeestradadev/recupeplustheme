<?php
/**
 * WPboilerplate Awesome WordPress Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage wpboilerplate
 * @since 1.0.0
 * @version 1.0.0
 *
 */

// Función recursiva para incluir archivos PHP de una carpeta y sus subcarpetas
function includeFilesFromFolder($folderPath) {
  $files = scandir($folderPath);
  foreach ($files as $file) {
    $filePath = $folderPath . '/' . $file;
    if ($file === '.' || $file === '..') {
        continue;
    }
    if (is_dir($filePath)) {
        includeFilesFromFolder($filePath);
    } elseif (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
      include_once $filePath;
    }
  }
}

// Incluyendo archivos del directorio 'core-functions' y sus subcarpetas
$folderPath = __DIR__ . '/core-functions';
includeFilesFromFolder($folderPath);
