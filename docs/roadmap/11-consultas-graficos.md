# Consultas eficientes nos gráficos

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 06.

## Evidência e objetivo

O histórico é pré-carregado, mas helpers como `AvaliationWeightGraphHelper::getAvaliation()` executam `Avaliation::find()` repetidamente.

Preparar dados uma vez por relatório e evitar consultas redundantes.

## Arquivos e componentes

`app/Helpers/AvaliationGraph/*`, `app/View/Components/AvaliationGraph.php`, `app/Models/Avaliation.php`.

## Implementação

1. Registrar número de queries em relatório curto e com dez avaliações históricas.
2. Passar avaliação atual e histórico aos helpers por construtor ou contexto imutável; preservar API pública durante migração.
3. Carregar relações necessárias explicitamente e remover acessos implícitos que disparem N+1.
4. Revisar os 14 helpers e cards, concentrando cálculos compartilhados.

## Critérios de aceite

- [ ] Número de consultas deixa de crescer proporcionalmente ao número de gráficos.
- [ ] Gráficos mostram os mesmos pontos e tabelas.
- [ ] Consulta de outro tenant não é introduzida pela otimização.

## Verificação

- Comparar contagem de queries antes/depois nos cenários 06.
- Testes dos pontos e rótulos de gráficos principais.
