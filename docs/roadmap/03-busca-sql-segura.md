# Busca e ordenação SQL seguras

- **Prioridade:** P0
- **Estado:** concluído
- **Esforço relativo:** P
- **Dependências:** Nenhuma.

## Evidência e objetivo

`ClientsTable` e `AvaliationsTable` interpolam `$searchBy` em `whereRaw`; `ClientsTable` também usa `orderByRaw` com entrada dinâmica.

Tratar busca como parâmetro e restringir direção de ordenação a valores permitidos.

## Arquivos e componentes

`app/Tables/ClientsTable.php`, `app/Tables/AvaliationsTable.php`, testes novos das tabelas.

## Implementação

1. Substituir interpolação no `LIKE` por parâmetro vinculado; definir tratamento de `%` e `_`.
2. Escolher `asc` ou `desc` em lista fechada; deixar expressão da coluna fixa.
3. Revisar demais consultas `Raw` que possam receber requisições.
4. Conferir paginação, nomes compostos e ambas direções após mudança.

## Critérios de aceite

- [x] Aspas e texto semelhante a SQL não mudam estrutura da consulta.
- [x] Busca e ordenação continuam corretas.
- [x] Direção inválida é rejeitada ou normalizada.

## Verificação

- Testes de HTTP/componente com entradas especiais e dois tenants.
- Inspecionar SQL gerado e parâmetros em teste.

## Implementado

- As buscas por nome composto usam parâmetro vinculado e escapam `%`, `_` e `!` para tratar o termo como texto literal.
- A ordenação por nome composto aceita `asc` e `desc`; qualquer outra direção é normalizada para `asc`.
- As demais consultas `Raw` em `app` foram revisadas e não interpolam entrada da requisição.
- `TableSqlSafetyTest` verifica resultados com dois profissionais, aspas, curingas, ordenação e vínculos SQL.
