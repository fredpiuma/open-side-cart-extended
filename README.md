# Open Side Cart Extended

Recursos extras para o carrinho lateral do WooCommerce. Funciona com o **Open Side Cart** e também com o **Side Cart by XootiX** (premium).

O objetivo é manter o cliente dentro do carrinho lateral: ele simula o frete, vê as formas de pagamento e edita o carrinho sem ir para a página do carrinho, que costuma ser lenta e tirar o foco da compra.

## Benefícios

- **Simulador de frete no carrinho lateral**: campo de CEP e botão "Calcular", abaixo dos produtos. Usa o cálculo de frete nativo do WooCommerce, então funciona com qualquer método já configurado (Correios, Frenet, frete fixo, frete grátis etc.). As opções aparecem só para consulta, com ícones e preços.
- **Formas de pagamento em destaque**: até 3 frases (ex.: "Parcele em até 6x sem juros no cartão", "5% de desconto no PIX"), editáveis pela página de opções, com ícones fixos.
- **Carrinho nativo bloqueado (opcional)**: quem acessa a página do carrinho vai para a home com o carrinho lateral aberto.
- **Mais caminhos para abrir o carrinho lateral**:
  - o link "Editar carrinho" do checkout (Fluid Checkout) abre o carrinho lateral, e o ícone flutuante fica oculto no checkout;
  - um seletor CSS configurável, para o ícone de carrinho do header do seu tema, por exemplo.
- **Carrinho atualizado ao aplicar ou remover cupom**: corrige o carrinho lateral que fica com o conteúdo desatualizado depois de mexer no cupom.
- **Tradução do botão "Update"** do quickview para "Atualizar".
- **Ajustes para produtos agrupados**: aparecem só se o site usa WPC Product Bundles (woosb) ou WPC Grouped Product (woosg).
  - Ocultar preços dos subprodutos no carrinho lateral.
  - Corrigir o nome dos subprodutos com mais de um atributo na variação, no pedido e no e-mail (ex.: "Top Fitness - M, Preto").
- **Tudo liga e desliga** pela página de opções, sem mexer em código nem no tema.
- **Compatível com as duas versões do Side Cart**: detecta automaticamente se o site usa o Open Side Cart ou o Side Cart by XootiX.

## Requisitos

- WordPress 6.0+
- PHP 7.4+
- WooCommerce
- Open Side Cart **ou** Side Cart by XootiX (WooCommerce Side Cart Premium)

## Instalação

### Pelo painel do WordPress

1. Baixe o ZIP do repositório: botão **Code → Download ZIP**, ou a página de [releases](https://github.com/fredpiuma/open-side-cart-extended/releases), se houver.
2. No WordPress, vá em **Plugins → Adicionar novo → Enviar plugin**, escolha o ZIP e clique em **Instalar agora**.
3. Ative o plugin.

### Via WP-CLI

```bash
wp plugin install https://github.com/fredpiuma/open-side-cart-extended/archive/refs/heads/main.zip --activate --force
```

O `--force` sobrescreve uma versão já instalada, então o mesmo comando também serve para atualizar.

### Via Git

```bash
cd wp-content/plugins
git clone https://github.com/fredpiuma/open-side-cart-extended.git
```

Depois ative o plugin em **Plugins**.

## Configuração

Vá em **WooCommerce → Side Cart Extended** e ligue os recursos que quiser. A página mostra qual Side Cart foi detectado.

| Opção | O que faz |
|---|---|
| Calcular frete no carrinho | Simulador de CEP no carrinho lateral |
| Bloquear abertura do carrinho nativo | Redireciona a página do carrinho para a home, com o carrinho lateral aberto |
| Formas de pagamento | Lista de frases com ícone. Cada linha tem texto editável e pode ser ativada ou desativada |
| Link "Editar carrinho" do checkout | Abre o carrinho lateral no checkout e oculta o ícone flutuante |
| Seletor que abre o Side Cart | Seletor CSS de links que devem abrir o carrinho lateral |
| Atualizar ao aplicar/remover cupom | Recarrega o conteúdo do carrinho lateral |
| Traduzir "Update" | Troca "Update" por "Atualizar" no quickview |
| Ocultar preços dos subprodutos | Só com woosb/woosg |
| Corrigir nome dos produtos com mais de um atributo | Só com woosb/woosg |

Se o WooCommerce ou nenhum Side Cart estiver ativo, o plugin mostra um aviso no painel e não faz nada.

## Para desenvolvedores

- Filtro `xsc_addons_formas_pagamento`: altera a lista de formas de pagamento (array de `['icone' => '', 'texto' => '']`).
- As classes CSS dos blocos usam o prefixo `xsc-` (`.xsc-frete`, `.xsc-pagamento`) e podem ser sobrescritas pelo tema.

## Licença

GPL-2.0-or-later. Autor: [Frederico de Castro](https://www.fredericodecastro.com.br/links).
