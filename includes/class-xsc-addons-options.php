<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Leitura das opções do plugin (option única 'xsc_addons_options'), sempre
 * mescladas com os valores padrão.
 */
class XSC_Addons_Options
{
	const OPTION = 'xsc_addons_options';

	/**
	 * Qual Side Cart está ativo: o premium da XootiX (Xoo WSC) ou o fork open
	 * source (Open Side Cart). Os dois têm os mesmos hooks, endpoints, eventos
	 * e classes CSS, só muda o prefixo ('xoo_wsc'/'xoo-wsc' → 'osc'/'osc').
	 *
	 * Retorna null se nenhum estiver ativo, ou:
	 * - 'nome':   nome para exibição;
	 * - 'php':    prefixo de hooks e endpoints wc-ajax (ex.: '{php}_after_products');
	 * - 'css':    prefixo das classes CSS (ex.: '.{css}-basket').
	 */
	public static function side_cart()
	{
		if (function_exists('osc_helper')) {
			return array('nome' => 'Open Side Cart', 'php' => 'osc', 'css' => 'osc');
		}

		if (function_exists('xoo_wsc_helper')) {
			return array('nome' => 'WooCommerce Side Cart (XootiX)', 'php' => 'xoo_wsc', 'css' => 'xoo-wsc');
		}

		return null;
	}

	/**
	 * Ícones fixos das formas de pagamento (um por linha da página de opções).
	 */
	public static function icones_pagamento()
	{
		return array('💳', '💰', '💵');
	}

	public static function defaults()
	{
		return array(
			// Carrinho
			'frete'                    => 1,
			'bloquear_carrinho'        => 0,
			'ocultar_frete_sem_cep'    => 0,
			'pagamento'                => 1,
			'pagamento_itens'          => array(
				array('texto' => 'Parcele em até 6x sem juros no cartão', 'ativo' => 1),
				array('texto' => '5% de desconto no PIX', 'ativo' => 1),
				array('texto' => 'Em até 4x sem juros no PIX parcelado', 'ativo' => 1),
			),

			// Extras
			'cupom_refresh'            => 1,
			'editar_carrinho_checkout' => 1,
			'seletor_abrir'            => '',
			'traduzir_update'          => 1,

			// Produtos agrupados (woosb / woosg)
			'ocultar_preco_filhos'     => 0,
			'corrigir_nome_variacao'   => 0,
		);
	}

	public static function all()
	{
		static $opcoes = null;

		if ($opcoes === null) {
			$salvas = get_option(self::OPTION, array());
			$opcoes = wp_parse_args(is_array($salvas) ? $salvas : array(), self::defaults());
		}

		return $opcoes;
	}

	public static function get($chave)
	{
		$opcoes = self::all();
		return isset($opcoes[$chave]) ? $opcoes[$chave] : null;
	}

	public static function ativo($chave)
	{
		return !empty(self::get($chave));
	}

	/**
	 * Formas de pagamento ativas e com texto, já com o ícone fixo de cada linha.
	 */
	public static function formas_pagamento()
	{
		$icones = self::icones_pagamento();
		$itens  = (array) self::get('pagamento_itens');
		$lista  = array();

		foreach ($icones as $i => $icone) {
			$item = isset($itens[$i]) ? $itens[$i] : array();

			if (!empty($item['ativo']) && !empty($item['texto'])) {
				$lista[] = array('icone' => $icone, 'texto' => $item['texto']);
			}
		}

		return apply_filters('xsc_addons_formas_pagamento', $lista);
	}

	/**
	 * Tipos de produto agrupado detectados no site: 'woosb' (WPC Product
	 * Bundles) e/ou 'woosg' (WPC Grouped Product). Conta o plugin ativo ou a
	 * existência de produtos desse tipo.
	 */
	public static function tipos_agrupados()
	{
		$tipos = array();

		foreach (array('woosb' => 'WPCleverWoosb', 'woosg' => 'WPCleverWoosg') as $tipo => $classe) {
			$termo = get_term_by('slug', $tipo, 'product_type');

			if (class_exists($classe) || ($termo && $termo->count > 0)) {
				$tipos[] = $tipo;
			}
		}

		return $tipos;
	}
}
