<?php
/**
 * @package Perfect_Cat
 * @version 0.0.1
 */
/*
Plugin Name: Perfect Creation
Plugin URI: 
Description: Плагин для управления и отображения котиков на сайте
Author: Yaugen Kavalevich
Version: 0.0.1
Author URI: https://www.linkedin.com/in/kavalevich/
*/

include_once 'CPT/Custom.php';
use CPT\Cat;

Cat::register();