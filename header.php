<?php
   /**
    * The header.
    *
    * This is the template that displays all of the <head> section and everything up until main.
    *
    * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
    *
    * @package WordPress
    * @subpackage Twenty_Twenty_One
    * @since Twenty Twenty-One 1.0
    */   
   ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php twentytwentyone_the_html_classes(); ?>>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><html class="no-js" lang="en-us"> <![endif]-->
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <!-- Viewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5, user-scalable=yes">
  <!-- IE Compatibility -->
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
  <!-- Apple Mobile Web App Settings -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <!-- Miscellaneous -->
  <meta name="format-detection" content="telephone=no">
  <meta name="MobileOptimized" content="width">
  <meta name="HandheldFriendly" content="true">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&amp;display=swap" rel="stylesheet">
  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
	<title><?php wp_title( ' | ', true, 'right' ); ?> <?php bloginfo( 'name' ); ?></title>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php get_template_part( 'template-parts/menu' ); ?>
	<main>

