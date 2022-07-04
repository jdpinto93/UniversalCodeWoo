<?php
 
/**
 * Plugin Name:       Universal Code
 * Plugin URI:        http://www.webmasteryagency.com
 * Description:       Incluye UPC EAN MPN En el inventario de Woocommerce, configuracion desde el inventario para mostrar u ocultar el campo deseado.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.8
 * Author:            Jose Pinto
 * Author URI:        http://www.webmasteryagency.com
 * License:           GPL v3 or later
 */

//Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
defined('ABSPATH') or die( "Bye bye" );

define('RAI_RUTA',plugin_dir_path(__FILE__));

// Archivos Externos


// Agrega el campo de UPC al inventario de Woocommerce
include(RAI_RUTA.'/wooCambios/upc/upc.php');

// Agrega el campo de MPN al inventario de Woocommerce
include(RAI_RUTA.'/wooCambios/mpn/mpn.php');

// Agrega el campo de EAN al inventario de Woocommerce
include(RAI_RUTA.'/wooCambios/ean/ean.php');