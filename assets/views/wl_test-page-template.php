<?php

/**
 * Template Name: WL Test Page Template
 * Description: A Page Template with a Login/Register block.
 */

get_header();
// including register form
@include __DIR__ . '/register.php';

get_footer();