# Planos mais claros

- **Prioridade:** P2
- **Estado:** planejado
- **Esforço relativo:** P
- **Dependências:** 15.

## Evidência e objetivo

A seção de preços do site lista muitos recursos nas duas colunas e diferencia indisponibilidade no Free principalmente por ícone.

Ajudar visitante a entender valor do Premium e o limite gratuito antes de se cadastrar.

## Arquivos e componentes

`resources/views/site/partials/versions-section.blade.php`, `app/Presenters/SubscriptionUpgradePresenter.php`, `resources/views/app/subscription/upgrade.blade.php`.

## Implementação

1. Revisar matriz real de recursos com middleware e `FeatureAbstract`; corrigir qualquer divergência entre site e aplicação.
2. Destacar três diferenças decisivas em texto: clientes, personalização/compartilhamento e acompanhamento avançado.
3. Adicionar exemplo do relatório Free e Premium e explicar a cobrança mensal equivalente sem ambiguidade.
4. Marcar recursos incluídos/indisponíveis por texto acessível, sem depender só de cor ou ícone.
5. Medir clique no CTA, início de checkout e conversão após mudança.

## Critérios de aceite

- [ ] Conteúdo do site corresponde às permissões efetivas.
- [ ] Diferenças são compreensíveis sem ícones ou cor.
- [ ] Preço e período apresentados concordam com checkout.

## Verificação

- Revisão manual em desktop, celular e leitor de tela.
- Teste de matriz de recursos quando alterar plano.
