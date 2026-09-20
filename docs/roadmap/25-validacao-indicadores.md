# Precisão dos indicadores

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** 21.

## Evidência e objetivo

Fórmulas corporais estão distribuídas por `app/Helpers/Avaliation` e `app/Models/Avaliation.php`; medidas usam `float`; há expressão de precedência suspeita em `AvaliationWeightGraphHelper`.

Garantir unidades, faixas e interpretação coerentes com os métodos de avaliação oferecidos.

## Arquivos e componentes

`app/Helpers/Avaliation/*`, `app/Helpers/AvaliationGraph/*`, `app/Models/Avaliation.php`, migrações de avaliações.

## Implementação

1. Inventariar indicador, fórmula, unidade, fonte, condições de validade, arredondamento e dados mínimos exigidos.
2. Montar casos de referência independentes com profissional habilitado para métodos de dobras, medidas e bioimpedância.
3. Separar cálculo bruto de classificação/apresentação; representar resultado indisponível explicitamente em vez de número sentinela no relatório.
4. Corrigir precedência e limites dos gráficos depois de comparar casos; revisar persistência `float` versus decimal para medidas-chave sem migração precipitada.
5. Versionar fórmulas quando mudança alterar relatórios históricos e informar método usado no PDF.

## Critérios de aceite

- [ ] Casos de referência batem com tolerância definida.
- [ ] Entradas faltantes ou fora da faixa não geram interpretação enganosa.
- [ ] Relatório informa método, unidade e versão quando aplicável.

## Verificação

- Testes parametrizados de limites, sexo, idade e dados incompletos.
- Revisão de amostras por profissional de domínio.
