<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Página de opções em WooCommerce → Side Cart Extended.
 */
class XSC_Addons_Admin
{
	const PAGINA = 'xsc-addons';
	const GRUPO  = 'xsc_addons';

	/**
	 * Opções sim/não, na ordem em que aparecem na página.
	 */
	private static function checkboxes()
	{
		return array(
			'frete', 'bloquear_carrinho', 'ocultar_frete_sem_cep', 'pagamento',
			'cupom_refresh', 'editar_carrinho_checkout', 'traduzir_update',
			'ocultar_preco_filhos', 'corrigir_nome_variacao',
		);
	}

	public static function init()
	{
		add_action('admin_menu', array(__CLASS__, 'menu'), 99);
		add_action('admin_init', array(__CLASS__, 'registrar'));
	}

	public static function menu()
	{
		add_submenu_page('woocommerce', 'Open Side Cart Extended', 'Side Cart Extended', 'manage_woocommerce', self::PAGINA, array(__CLASS__, 'pagina'));
	}

	public static function registrar()
	{
		register_setting(self::GRUPO, XSC_Addons_Options::OPTION, array(
			'type'              => 'array',
			'sanitize_callback' => array(__CLASS__, 'sanitizar'),
		));
	}

	public static function sanitizar($entrada)
	{
		$entrada  = is_array($entrada) ? $entrada : array();
		$anterior = XSC_Addons_Options::all();
		$saida    = array();

		// Checkbox desmarcado não é enviado, por isso grava 0 explicitamente
		// (senão o default voltaria a valer).
		foreach (self::checkboxes() as $chave) {
			$saida[$chave] = empty($entrada[$chave]) ? 0 : 1;
		}

		// A seção de produtos agrupados só é exibida quando detectada: se não
		// está na página, mantém os valores que já estavam salvos.
		if (!XSC_Addons_Options::tipos_agrupados()) {
			$saida['ocultar_preco_filhos']   = $anterior['ocultar_preco_filhos'];
			$saida['corrigir_nome_variacao'] = $anterior['corrigir_nome_variacao'];
		}

		$saida['seletor_abrir'] = isset($entrada['seletor_abrir']) ? sanitize_text_field($entrada['seletor_abrir']) : '';

		$saida['pagamento_itens'] = array();
		foreach (XSC_Addons_Options::icones_pagamento() as $i => $icone) {
			$item = isset($entrada['pagamento_itens'][$i]) ? $entrada['pagamento_itens'][$i] : array();

			$saida['pagamento_itens'][$i] = array(
				'texto' => isset($item['texto']) ? sanitize_text_field($item['texto']) : '',
				'ativo' => empty($item['ativo']) ? 0 : 1,
			);
		}

		return $saida;
	}

	private static function nome($chave)
	{
		return XSC_Addons_Options::OPTION . '[' . $chave . ']';
	}

	private static function checkbox($chave, $rotulo, $descricao = '')
	{
		?>
		<tr>
			<th scope="row"><?php echo esc_html($rotulo); ?></th>
			<td>
				<label>
					<input type="checkbox" name="<?php echo esc_attr(self::nome($chave)); ?>" value="1" <?php checked(XSC_Addons_Options::ativo($chave)); ?>>
					Sim
				</label>
				<?php if ($descricao): ?>
					<p class="description"><?php echo esc_html($descricao); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	public static function pagina()
	{
		if (!current_user_can('manage_woocommerce')) {
			return;
		}

		$itens     = (array) XSC_Addons_Options::get('pagamento_itens');
		$agrupados = XSC_Addons_Options::tipos_agrupados();
		$nomes     = array('woosb' => 'WPC Product Bundles (woosb)', 'woosg' => 'WPC Grouped Product (woosg)');
		?>
		<div class="wrap">
			<h1>Open Side Cart Extended</h1>
			<?php $side_cart = XSC_Addons_Options::side_cart(); ?>
			<p>Melhorias para o carrinho lateral. Side Cart detectado: <strong><?php echo esc_html($side_cart ? $side_cart['nome'] : 'nenhum'); ?></strong>.</p>

			<form method="post" action="options.php">
				<?php settings_fields(self::GRUPO); ?>

				<h2 class="title">Carrinho</h2>
				<table class="form-table" role="presentation">
					<?php
					self::checkbox('frete', 'Calcular frete no carrinho', 'Campo de CEP e botão Calcular no Side Cart, abaixo dos produtos. Usa o cálculo de frete nativo do WooCommerce; as opções são só exibidas, sem escolha.');
					self::checkbox('bloquear_carrinho', 'Bloquear abertura do carrinho nativo', 'Quem acessar a página do carrinho é redirecionado para a home, e o Side Cart abre automaticamente.');
					self::checkbox('ocultar_frete_sem_cep', 'Esconder frete quando sessão sem CEP', 'Remove a linha "Frete" dos totais do Side Cart enquanto o cliente não informou o CEP, evitando que apareça "Grátis" antes do cálculo. Se a calculadora nativa do Side Cart estiver ativa, o atalho para ela (que fica nessa linha) também some até haver CEP; o simulador de frete acima cobre esse papel.');
					self::checkbox('pagamento', 'Formas de pagamento', 'Lista de formas de pagamento no Side Cart, abaixo do simulador de frete.');
					?>
					<tr>
						<th scope="row">Frases das formas de pagamento</th>
						<td>
							<?php foreach (XSC_Addons_Options::icones_pagamento() as $i => $icone):
								$item = isset($itens[$i]) ? $itens[$i] : array('texto' => '', 'ativo' => 0);
								$base = self::nome('pagamento_itens') . '[' . $i . ']';
							?>
								<p>
									<span style="display:inline-block;width:1.6em;font-size:1.2em;" aria-hidden="true"><?php echo esc_html($icone); ?></span>
									<input type="text" class="regular-text" name="<?php echo esc_attr($base . '[texto]'); ?>" value="<?php echo esc_attr($item['texto']); ?>">
									<label>
										<input type="checkbox" name="<?php echo esc_attr($base . '[ativo]'); ?>" value="1" <?php checked(!empty($item['ativo'])); ?>>
										Ativo
									</label>
								</p>
							<?php endforeach; ?>
							<p class="description">Os ícones são fixos. Linhas inativas ou sem texto não aparecem.</p>
						</td>
					</tr>
				</table>

				<h2 class="title">Abrir o Side Cart</h2>
				<table class="form-table" role="presentation">
					<?php
					self::checkbox('editar_carrinho_checkout', 'Link "Editar carrinho" do checkout', 'No checkout (Fluid Checkout), o link "Editar carrinho" abre o Side Cart, e o ícone flutuante do Side Cart fica oculto nessa página.');
					?>
					<tr>
						<th scope="row"><label for="xsc-seletor-abrir">Seletor que abre o Side Cart</label></th>
						<td>
							<input type="text" id="xsc-seletor-abrir" class="large-text code" name="<?php echo esc_attr(self::nome('seletor_abrir')); ?>" value="<?php echo esc_attr(XSC_Addons_Options::get('seletor_abrir')); ?>" placeholder="#site-mobile-header .g5shop__mini-cart-icon a">
							<p class="description">Seletor CSS de links/botões (ex.: ícone do carrinho no header) que devem abrir o Side Cart em vez de ir para o carrinho. Vazio = desligado.</p>
						</td>
					</tr>
				</table>

				<h2 class="title">Ajustes</h2>
				<table class="form-table" role="presentation">
					<?php
					self::checkbox('cupom_refresh', 'Atualizar ao aplicar/remover cupom', 'Recarrega o conteúdo do Side Cart depois de aplicar ou remover um cupom (o plugin não atualiza sozinho).');
					self::checkbox('traduzir_update', 'Traduzir "Update"', 'Troca o texto "Update" do botão do quickview do Side Cart por "Atualizar".');
					?>
				</table>

				<?php if ($agrupados): ?>
					<h2 class="title">Produtos agrupados</h2>
					<p>Detectado: <?php echo esc_html(implode(', ', array_intersect_key($nomes, array_flip($agrupados)))); ?>.</p>
					<table class="form-table" role="presentation">
						<?php
						self::checkbox('ocultar_preco_filhos', 'Ocultar preços dos subprodutos', 'No Side Cart, esconde o preço e o subtotal dos itens que fazem parte de um pacote.');
						self::checkbox('corrigir_nome_variacao', 'Corrigir nome dos produtos com mais de um atributo na variação', 'Na lista de conteúdo do pacote no pedido/e-mail, monta o nome com o título do produto + todos os atributos da variação (ex.: "Top Fitness - M, Preto").');
						?>
					</table>
				<?php endif; ?>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
