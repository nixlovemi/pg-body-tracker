# Gráficos fora do caminho externo crítico

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** 06 e 26.

## Evidência e objetivo

`ChartPhp::initBase64Img()` chama QuickChart por gráfico ausente no cache; o PDF pode renderizar 14 configurações em sequência.

Reduzir latência e dependência de rede e controlar o destino dos dados gráficos.

## Arquivos e componentes

`app/View/Components/ChartPhp.php`, `app/Presenters/AvaliationReportPresenter.php`, `app/Helpers/AvaliationGraph/*`, `resources/views/components/avaliation-graph-abstract.blade.php`.

## Implementação

1. Usar a linha de base 06 para contar chamadas, tempo e falhas de cada gráfico.
2. Prototipar geração local de PNG/SVG compatível com os gráficos existentes e comparar fidelidade no PDF, memória e latência.
3. Definir um renderizador atrás de interface única, preservando o atual durante migração e permitindo ativação por configuração.
4. Migrar os tipos de gráfico progressivamente, mantendo testes visuais de amostra e cache por configuração, tamanho e versão.
5. Atualizar o mapa de dados de terceiros 26 conforme a solução final.

## Critérios de aceite

- [ ] PDF completo mantém valores, cores, eixos e rótulos dos gráficos.
- [ ] A geração normal não depende de chamadas ao QuickChart após migração.
- [ ] Tempo e falhas são comparados com a linha de base.

## Verificação

- Comparar PDFs de casos com 0, 1 e 10 avaliações anteriores.
- Medir cache frio e quente e inspecionar páginas geradas.
