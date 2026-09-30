/**
 * XSC Addons — comportamento no Side Cart (XootiX premium ou Open Side Cart).
 * As flags vêm de wp_localize_script ('xscAddons'), conforme a página de
 * opções, junto com os prefixos do Side Cart ativo ('xoo_wsc'/'xoo-wsc' ou
 * 'osc'/'osc'), usados para montar endpoints, eventos e classes.
 */
jQuery(function ($) {
    if (typeof xscAddons === 'undefined') {
        return;
    }

    var php = xscAddons.prefixoPhp;
    var css = xscAddons.prefixoCss;
    var seletorBasket = '.' + css + '-basket';

    // Endpoint do Side Cart, ex.: 'calculate_shipping' → 'osc_calculate_shipping'.
    function wcAjaxUrl(acao) {
        return xscAddons.wcAjaxUrl.toString().replace('%%endpoint%%', php + '_' + acao);
    }

    function aplicarFragments(response) {
        if (response && response.fragments) {
            $.each(response.fragments, function (key, value) {
                $(key).replaceWith(value);
            });
            $(document.body).trigger('wc_fragments_refreshed');
        }
    }

    function abrirSideCart() {
        var $basket = $(seletorBasket);

        if (!$basket.length) {
            return false;
        }

        $basket.trigger('click');
        return true;
    }

    // O Side Cart não atualiza o HTML após aplicar/remover um cupom (a sessão
    // muda, mas o conteúdo fica desatualizado). Chama o endpoint de refresh de
    // fragments do próprio plugin e aplica o HTML atualizado.
    if (xscAddons.cupomRefresh) {
        var refreshFragments = function () {
            var url = wcAjaxUrl('refresh_fragments');

            if (url) {
                $.post(url, {}, aplicarFragments);
            }
        };

        // O plugin dispara '{prefixo}_coupon_removed' no fim do ajax de remoção.
        $(document.body).on(php + '_coupon_removed', refreshFragments);

        // Na aplicação, espera o ajax do próprio plugin terminar.
        $(document).on('submit', 'form.' + css + '-sl-apply-coupon', function () {
            $(document).one('ajaxComplete', refreshFragments);
        });
    }

    // Links que devem abrir o Side Cart em vez de ir para o carrinho ("Editar
    // carrinho" do checkout e/ou seletor configurado). Sem o Side Cart na
    // página, o link segue normalmente.
    if (xscAddons.seletoresAbrir) {
        $(document).on('click', xscAddons.seletoresAbrir, function (event) {
            if (!$(seletorBasket).length) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            abrirSideCart();
        });
    }

    // Página do carrinho bloqueada: o redirect leva para a home com
    // ?abrir-carrinho=1. Abre o Side Cart e tira o parâmetro da URL. Espera o
    // load para o Side Cart já estar iniciado.
    if (xscAddons.abrirCarrinho && /[?&]abrir-carrinho=1(&|$)/.test(window.location.search)) {
        $(window).on('load', function () {
            abrirSideCart();

            if (window.history && window.history.replaceState) {
                var url = window.location.href.replace(/([?&])abrir-carrinho=1(&|$)/, function (trecho, antes, depois) {
                    return depois ? antes : '';
                });
                window.history.replaceState(null, '', url);
            }
        });
    }

    if (xscAddons.frete) {
        // Máscara 00000-000 no CEP.
        $(document).on('input', '.xsc-frete__cep', function () {
            var cep = this.value.replace(/\D/g, '').slice(0, 8);
            this.value = cep.length > 5 ? cep.slice(0, 5) + '-' + cep.slice(5) : cep;
        });

        // Envia o CEP para o endpoint de cálculo de frete do próprio Side Cart,
        // que usa o cálculo nativo do WooCommerce e devolve os fragments do
        // carrinho já com o bloco de frete renderizado com as opções.
        $(document).on('submit', 'form.xsc-frete__form', function (event) {
            event.preventDefault();

            var url = wcAjaxUrl('calculate_shipping');
            var $form = $(this);
            var $campo = $form.find('.xsc-frete__cep');
            var $botao = $form.find('.xsc-frete__botao');
            var cep = $campo.val().replace(/\D/g, '');

            if (!url) {
                return;
            }

            if (cep.length !== 8) {
                $campo.trigger('focus');
                return;
            }

            $botao.prop('disabled', true).text(xscAddons.textoCalculando);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    calc_shipping_country: 'BR',
                    calc_shipping_state: '',
                    calc_shipping_city: '',
                    calc_shipping_postcode: cep
                },
                success: aplicarFragments,
                complete: function () {
                    // Se os fragments não substituíram o form, reabilita o botão.
                    $botao.prop('disabled', false).text(xscAddons.textoCalcular);
                }
            });
        });
    }
});
