# Open Side Cart Extended

Plugin WordPress com recursos extras para o carrinho lateral do WooCommerce (simulador de frete, formas de pagamento, bloqueio do carrinho nativo, atalhos e ajustes para produtos agrupados). Funciona com o Open Side Cart e com o Side Cart by XootiX. Arquivo principal: `xsc-addons.php` (não renomear: desativaria o plugin nos sites onde já está instalado); slug/pasta: `open-side-cart-extended`; text domain e prefixo: `xsc-addons` / `xsc_addons`.

## Versões e releases

Toda melhoria ou correção entregue vira uma versão nova publicada no GitHub. Não termine a tarefa sem o release.

1. Escolha a versão (SemVer): correção → `X.Y.Z+1`; recurso novo ou melhoria → `X.Y+1.0`; quebra de compatibilidade → `X+1.0.0`.
2. Atualize a versão em todos estes lugares: cabeçalho `Version:` de `xsc-addons.php` e a constante `XSC_ADDONS_VERSION` no mesmo arquivo.
3. Acrescente a entrada `## [X.Y.Z] - AAAA-MM-DD` no topo do `CHANGELOG.md`, em português, dizendo o que mudou para quem usa o plugin.
4. Confira `php -l` nos arquivos PHP alterados.
5. Commit, push da `main`, e crie o release com as notas tiradas do CHANGELOG:
   `gh release create vX.Y.Z --title "Open Side Cart Extended X.Y.Z" --notes-file <notas.md> --latest`
   (não anexe arquivos: o workflow `.github/workflows/release.yml` gera e anexa `open-side-cart-extended.zip` e `open-side-cart-extended-X.Y.Z.zip`).
6. Acompanhe o workflow: `gh run list --workflow release.yml -L 1` e `gh run watch <id> --exit-status`.
7. Confirme: `gh release view vX.Y.Z --json assets -q '.assets[].name'` lista os dois zips, e
   `curl -sIL -o /dev/null -w "%{http_code}" https://github.com/fredpiuma/open-side-cart-extended/releases/latest/download/open-side-cart-extended.zip` responde `200`.
8. Se o workflow falhar por erro no próprio workflow: corrija na `main` e recrie release e tag
   (`gh release delete vX.Y.Z --yes --cleanup-tag` + `gh release create`), porque o workflow usa o código da tag.
