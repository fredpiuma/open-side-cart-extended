<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Recursos no site. Cada hook só é registrado com a sua opção ligada.
 */
class XSC_Addons_Frontend
{
	public static function init()
	{
		$o    = 'XSC_Addons_Options';
		$hook = $o::side_cart()['php'] . '_after_products';

		add_action('wp_enqueue_scripts', array(__CLASS__, 'assets'), 100);
		add_filter('body_class', array(__CLASS__, 'body_class'));

		// '{prefixo}_after_products' dispara no template {prefixo}-body.php só
		// com o carrinho não vazio. O corpo do Side Cart é recarregado via
		// fragments a cada alteração, então estes blocos são renderizados de novo.
		if ($o::ativo('frete')) {
			add_action($hook, array(__CLASS__, 'bloco_frete'), 5);
		}

		if ($o::ativo('pagamento')) {
			add_action($hook, array(__CLASS__, 'bloco_pagamento'), 10);
		}

		// '{prefixo}_cart_totals' é aplicado no fim de get_totals() do Side Cart.
		if ($o::ativo('ocultar_frete_sem_cep')) {
			add_filter($o::side_cart()['php'] . '_cart_totals', array(__CLASS__, 'ocultar_frete_sem_cep'));
		}

		if ($o::ativo('bloquear_carrinho')) {
			add_action('template_redirect', array(__CLASS__, 'bloquear_carrinho'));
		}

		if ($o::ativo('traduzir_update')) {
			add_filter('woocommerce_product_single_add_to_cart_text', array(__CLASS__, 'traduzir_update'), 1000);
		}

		// A detecção de woosb/woosg (tipos_agrupados) depende da taxonomia
		// product_type, registrada só no init; aqui basta a opção, e o filtro
		// é do próprio WPC Product Bundles.
		if ($o::ativo('corrigir_nome_variacao')) {
			add_filter('woosb_order_bundled_product_name', array(__CLASS__, 'nome_subproduto_pedido'), 9999, 2);
		}
	}

	public static function assets()
	{
		$o  = 'XSC_Addons_Options';
		$sc = $o::side_cart();

		wp_enqueue_style('xsc-addons', XSC_ADDONS_URL . 'assets/xsc-addons.css', array(), XSC_ADDONS_VERSION);
		wp_enqueue_script('xsc-addons', XSC_ADDONS_URL . 'assets/xsc-addons.js', array('jquery'), XSC_ADDONS_VERSION, true);

		$seletores = array();

		if ($o::ativo('editar_carrinho_checkout')) {
			$seletores[] = 'a.fc-checkout-order-review__edit-cart';
		}

		if ($o::get('seletor_abrir')) {
			$seletores[] = $o::get('seletor_abrir');
		}

		wp_localize_script('xsc-addons', 'xscAddons', array(
			// Prefixos do Side Cart ativo, usados para montar endpoints, evento
			// e classes CSS no JS. A URL do wc-ajax vem do próprio WooCommerce.
			'prefixoPhp'      => $sc['php'],
			'prefixoCss'      => $sc['css'],
			'wcAjaxUrl'       => WC_AJAX::get_endpoint('%%endpoint%%'),
			'frete'           => $o::ativo('frete'),
			'cupomRefresh'    => $o::ativo('cupom_refresh'),
			'abrirCarrinho'   => $o::ativo('bloquear_carrinho'),
			'seletoresAbrir'  => implode(', ', $seletores),
			'textoCalcular'   => 'Calcular',
			'textoCalculando' => 'Calculando...',
		));
	}

	/**
	 * Classes no body que ligam as regras de CSS opcionais.
	 */
	public static function body_class($classes)
	{
		$o = 'XSC_Addons_Options';

		if ($o::ativo('editar_carrinho_checkout')) {
			$classes[] = 'xsc-ocultar-basket-checkout';
		}

		if ($o::tipos_agrupados() && $o::ativo('ocultar_preco_filhos')) {
			$classes[] = 'xsc-ocultar-preco-filhos';
		}

		return $classes;
	}

	/**
	 * Simulador de frete no Side Cart, acima das formas de pagamento.
	 *
	 * O form só tem o CEP; o JS envia para o endpoint
	 * 'wc-ajax={prefixo}_calculate_shipping' do próprio Side Cart, que roda o
	 * cálculo nativo do WooCommerce (WC_Shortcode_Cart::calculate_shipping) e
	 * devolve os fragments já recalculados. Assim este bloco é renderizado de
	 * novo com as opções de frete, que são só exibição (sem radio / sem escolha).
	 */
	public static function bloco_frete()
	{
		if (!WC()->cart || !WC()->cart->needs_shipping()) {
			return;
		}

		$cep       = WC()->customer ? WC()->customer->get_shipping_postcode() : '';
		$calculado = $cep && WC()->customer->has_calculated_shipping();
		$opcoes    = array();

		if ($calculado) {
			// Fora do ajax do Side Cart os pacotes podem não estar calculados
			// ainda (as taxas ficam em cache na sessão, então não refaz a cotação).
			if (empty(WC()->shipping()->get_packages())) {
				WC()->cart->calculate_shipping();
			}

			foreach (WC()->shipping()->get_packages() as $pacote) {
				foreach ($pacote['rates'] as $taxa) {
					$opcoes[] = $taxa;
				}
			}
		}
		?>
		<div class="xsc-frete">
			<form class="xsc-frete__form" novalidate>
				<label class="xsc-frete__titulo" for="xsc-frete-cep">🚚 Calcular frete</label>
				<div class="xsc-frete__campos">
					<input type="text" id="xsc-frete-cep" class="xsc-frete__cep" name="calc_shipping_postcode" value="<?php echo esc_attr($cep); ?>" placeholder="00000-000" inputmode="numeric" autocomplete="postal-code" maxlength="9">
					<button type="submit" class="xsc-frete__botao">Calcular</button>
				</div>
			</form>

			<?php if ($calculado): ?>
				<?php if ($opcoes): ?>
					<ul class="xsc-frete__opcoes">
						<?php foreach ($opcoes as $taxa): ?>
							<li>
								<span class="xsc-frete__icone" aria-hidden="true"><?php echo 'free_shipping' === $taxa->get_method_id() ? '🎁' : '📦'; ?></span>
								<span><?php echo wp_kses_post(wc_cart_totals_shipping_method_label($taxa)); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p class="xsc-frete__vazio">Nenhuma opção de frete disponível para este CEP.</p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Formas de pagamento no Side Cart, com ícone à esquerda.
	 */
	public static function bloco_pagamento()
	{
		$formas = XSC_Addons_Options::formas_pagamento();

		if (empty($formas)) {
			return;
		}
		?>
		<div class="xsc-pagamento">
			<ul class="xsc-pagamento__lista">
				<?php foreach ($formas as $forma): ?>
					<li>
						<span class="xsc-pagamento__icone" aria-hidden="true"><?php echo esc_html($forma['icone']); ?></span>
						<span><?php echo esc_html($forma['texto']); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Sem CEP na sessão, o Side Cart mostra "Free!"/"Grátis!" na linha de
	 * frete: get_totals() usa WC()->cart->get_cart_shipping_total() sempre que
	 * o pacote tem tarifas, e sem método escolhido o frete fica zero (o
	 * WooCommerce começa esse texto com __('Free!')). Remove a linha até o
	 * cliente informar o CEP.
	 *
	 * Os totais são refeitos a cada atualização via fragments: ao calcular pelo
	 * simulador (WC_Shortcode_Cart::calculate_shipping grava o CEP na sessão),
	 * a linha volta sozinha.
	 */
	public static function ocultar_frete_sem_cep($totals)
	{
		if (!WC()->customer || '' === trim((string) WC()->customer->get_shipping_postcode())) {
			unset($totals['shipping']);
		}

		return $totals;
	}

	/**
	 * Página do carrinho → home com ?abrir-carrinho=1 (o JS abre o Side Cart).
	 *
	 * Roda no template_redirect, depois do WC_Form_Handler (wp_loaded), então
	 * ações enviadas para o carrinho (ex.: ?add-to-cart=) são processadas antes.
	 * Redirect 302 para o navegador não guardar em cache.
	 */
	public static function bloquear_carrinho()
	{
		if (!is_cart()) {
			return;
		}

		wp_safe_redirect(add_query_arg('abrir-carrinho', '1', home_url('/')), 302);
		exit;
	}

	/**
	 * "Update" do botão do quickview do Side Cart vem fixo no plugin (sem
	 * __()), então não dá para traduzir pelo Loco Translate.
	 */
	public static function traduzir_update($texto)
	{
		return $texto === 'Update' ? 'Atualizar' : $texto;
	}

	/**
	 * Nome de cada produto em "Conteúdo do pacote" no pedido/e-mail (WPC
	 * Product Bundles). O título salvo na variação nem sempre traz todos os
	 * atributos (ex.: "Modelo" além de "Tamanho"), então o nome é remontado
	 * com o título do produto pai + os valores dos atributos da variação.
	 */
	public static function nome_subproduto_pedido($html, $item)
	{
		if (empty($item['id'])) {
			return $html;
		}

		$produto = wc_get_product($item['id']);

		if (!$produto instanceof WC_Product_Variation) {
			return $html;
		}

		$nome      = get_the_title($produto->get_parent_id());
		$atributos = wc_get_formatted_variation($produto, true, false, false);

		if ($atributos !== '') {
			$nome .= ' - ' . $atributos;
		}

		$texto = $item['qty'] . ' × ' . $nome;

		return (strpos($html, '<li>') === 0) ? '<li>' . $texto . '</li>' : $texto;
	}
}
