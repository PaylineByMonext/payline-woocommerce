<?php
/**
 * Plugin Name: Payline
 * Plugin URI: http://www.payline.com
 * Description: integrations of Payline payment solution in your WooCommerce store
 * Version: 1.4
 * Author: Monext
 * Author URI: http://www.monext.fr
 * License: LGPL-3.0+
 * GitHub Plugin URI: https://github.com/PaylineByMonext/payline-woocommerce/
 * Github Branch: master
 * 
 *  Copyright 2017  Monext  (email : support@payline.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ABSPATH')) exit;

define('WCPAYLINE_PLUGIN_URL', plugin_dir_url(__FILE__));

function woocommerce_payline_activation() {
	if (!is_plugin_active('woocommerce/woocommerce.php')) {
		deactivate_plugins(plugin_basename(__FILE__));

		load_plugin_textdomain('payline', false, dirname(plugin_basename(__FILE__)) . '/languages/');
		
		$message = sprintf(__('Sorry! In order to use WooCommerce %s Payment plugin, you need to install and activate the WooCommerce plugin.', 'payline'), 'Payline');
		wp_die($message, 'WooCommerce Payline Gateway Plugin', array('back_link' => true));
	}
}
register_activation_hook(__FILE__, 'woocommerce_payline_activation');

// inserts class gateway
function woocommerce_payline_init() {
	// Load translation files
	load_plugin_textdomain('payline', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	
	if (!class_exists('WC_Gateway_Payline')) {
		require_once 'class-wc-gateway-payline.php';
	}
	
	require_once 'vendor/autoload.php';
}
add_action('woocommerce_init', 'woocommerce_payline_init');


// adds method to woocommerce methods
function woocommerce_payline_add_method($methods) {
	$methods[] = 'WC_Gateway_Payline';
	return $methods;
}
add_filter('woocommerce_payment_gateways', 'woocommerce_payline_add_method');

// add a link from plugin list to parameters
function woocommerce_payline_add_link($links, $file) {
	$links[] = '<a href="'.admin_url('admin.php?page=wc-settings&tab=checkout&section=payline').'">' . __('Settings') .'</a>';
	return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'woocommerce_payline_add_link',  10, 2);
