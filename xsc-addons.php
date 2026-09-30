<?php
/**
 * Plugin Name: Open Side Cart Extended
 * Plugin URI: https://github.com/fredpiuma/open-side-cart-extended
 * Description: Recursos extras para o Open Side Cart, compatível também com o Side Cart by XootiX: simulador de frete, formas de pagamento, bloqueio da página do carrinho, atalhos para abrir o Side Cart e ajustes para produtos agrupados (WPC Product Bundles / Grouped).
 * Version: 1.0.0
 * Author: Frederico de Castro
 * Author URI: https://www.fredericodecastro.com.br/links
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: xsc-addons
 * Requires Plugins: woocommerce
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
	exit;
}

define('XSC_ADDONS_VERSION', '1.0.0');
define('XSC_ADDONS_PATH', plugin_dir_path(__FILE__));
define('XSC_ADDONS_URL', plugin_dir_url(__FILE__));

require_once XSC_ADDONS_PATH . 'includes/class-xsc-addons-options.php';
require_once XSC_ADDONS_PATH . 'includes/class-xsc-addons-admin.php';
require_once XSC_ADDONS_PATH . 'includes/class-xsc-addons-frontend.php';

/**
 * Link "Configurações" na lista de plugins.
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
	array_unshift($links, '<a href="' . esc_url(admin_url('admin.php?page=xsc-addons')) . '">Configurações</a>');
	return $links;
});

/**
 * Tudo depende do WooCommerce e de um Side Cart (premium ou open source). Sem
 * eles, só mostra um aviso no admin e não registra nada.
 */
add_action('plugins_loaded', function () {
	$faltando = array();

	if (!class_exists('WooCommerce')) {
		$faltando[] = 'WooCommerce';
	}

	if (!XSC_Addons_Options::side_cart()) {
		$faltando[] = 'WooCommerce Side Cart (XootiX) ou Open Side Cart';
	}

	if ($faltando) {
		add_action('admin_notices', function () use ($faltando) {
			echo '<div class="notice notice-warning"><p><strong>Open Side Cart Extended:</strong> precisa dos plugins ativos: ' . esc_html(implode(', ', $faltando)) . '.</p></div>';
		});
		return;
	}

	XSC_Addons_Admin::init();
	XSC_Addons_Frontend::init();
}, 20);
