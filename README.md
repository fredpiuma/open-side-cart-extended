# Open Side Cart Extended

Recursos extras para o carrinho lateral do WooCommerce. Funciona com o **Open Side Cart** e também com o **Side Cart by XootiX** (premium).

O objetivo é manter o cliente dentro do carrinho lateral: ele simula o frete, vê as formas de pagamento e edita o carrinho sem ir para a página do carrinho, que costuma ser lenta e tirar o foco da compra.

## Instalação

**[Baixar o plugin (zip da última versão)](https://github.com/fredpiuma/open-side-cart-extended/releases/latest/download/open-side-cart-extended.zip)**

Esse zip tem só os arquivos do plugin. Não use o "Source code (zip)" nem o **Code → Download ZIP** do GitHub para instalar: eles trazem arquivos de desenvolvimento e a pasta fica com o nome errado.

### Pelo painel do WordPress

1. Baixe o zip pelo link acima.
2. Em **Plugins → Adicionar novo → Enviar plugin**, escolha o arquivo e clique em **Instalar agora**.
3. Ative o **Open Side Cart Extended**.

Para atualizar, envie o zip da versão nova da mesma forma e confirme a substituição da versão instalada.

### Pelo WP-CLI

```bash
wp plugin install https://github.com/fredpiuma/open-side-cart-extended/releases/latest/download/open-side-cart-extended.zip --force --activate
```

O mesmo comando instala e atualiza: o `--force` substitui a versão instalada pela última, já que o plugin não tem atualização automática pelo painel.

### Pelo Git (para desenvolvimento)

```bash
cd wp-content/plugins
git clone https://github.com/fredpiuma/open-side-cart-extended.git
```

Depois ative o plugin em **Plugins**.

Requer WordPress 6.0+, PHP 7.4+, WooCommerce e o Open Side Cart **ou** o Side Cart by XootiX (WooCommerce Side Cart Premium).

## Benefícios

- **Simulador de frete no carrinho lateral**: campo de CEP e botão "Calcular", abaixo dos produtos. Usa o cálculo de frete nativo do WooCommerce, então funciona com qualquer método já configurado (Correios, Frenet, frete fixo, frete grátis etc.). As opções aparecem só para consulta, com ícones e preços.
- **Sem "Grátis" falso no frete (opcional)**: numa sessão nova, o carrinho lateral pode mostrar o frete como "Grátis!" antes de o cliente informar o CEP, mesmo sem frete grátis na loja. A opção esconde a linha de frete até existir um CEP na sessão.
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

## Configuração

Vá em **WooCommerce → Side Cart Extended** e ligue os recursos que quiser. A página mostra qual Side Cart foi detectado.

| Opção | O que faz |
|---|---|
| Calcular frete no carrinho | Simulador de CEP no carrinho lateral |
| Bloquear abertura do carrinho nativo | Redireciona a página do carrinho para a home, com o carrinho lateral aberto |
| Esconder frete quando sessão sem CEP | Remove a linha "Frete" dos totais até o cliente informar o CEP, evitando o "Grátis" antes do cálculo. O atalho da calculadora nativa, que fica nessa linha, também some até haver CEP |
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

## Versões

Veja no [CHANGELOG.md](CHANGELOG.md) o que mudou em cada versão.

## Licença

GPL-2.0-or-later. Autor: [Frederico de Castro](https://www.fredericodecastro.com.br/links).

---

### Precisa de uma solução sob medida para a sua loja?

Desenvolvo plugins, integrações e melhorias para WordPress e WooCommerce, do checkout ao pós-venda. Me conte o que você precisa e vamos encontrar o melhor caminho para o seu projeto.

**Frederico de Castro** · Senior Web Developer & Software Architect

[![Fale comigo no WhatsApp](https://img.shields.io/badge/Fale_comigo_no_WhatsApp-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://wa.me/5511914636944)
[![E-mail](https://img.shields.io/badge/E--mail-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:contato@fredericodecastro.com.br)
[![Site](https://img.shields.io/badge/Site-1F2937?style=for-the-badge&logo=googlechrome&logoColor=white)](https://www.fredericodecastro.com.br/links/)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0A66C2?style=for-the-badge)](https://www.linkedin.com/in/fredericodecastro/)
[![Telegram](https://img.shields.io/badge/Telegram-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)](https://t.me/fredericomdecastro)
