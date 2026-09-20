# Relatórios e envios em lotes

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 06.

## Evidência e objetivo

`ReportAbstract` usa `get()` para todas as linhas de HTML e CSV; `CheckinDispatchService` carrega todas as configurações ativas com `get()`.

Manter memória e tempo previsíveis com crescimento da base.

## Arquivos e componentes

`app/Helpers/Report/ReportAbstract.php`, `app/Http/Controllers/Report.php`, `app/Services/Checkin/CheckinDispatchService.php`.

## Implementação

1. Definir limites de página para HTML e contrato de exportação CSV grande.
2. Usar paginação ou cursor/chunk na exportação, mantendo cabeçalho e escape CSV; avaliar job para arquivos grandes.
3. Processar configurações de check-in em lotes com chave estável e pré-carregamento de relações.
4. Adicionar limites e métricas de linhas, duração, memória e erros por lote.
5. Revisar índices das consultas por usuário, data e status com `EXPLAIN` em volume representativo.

## Critérios de aceite

- [ ] Relatório HTML não carrega toda a base.
- [ ] Exportação e dispatch completam volume representativo com memória estável.
- [ ] Filtros por profissional e resultados mantêm equivalência.

## Verificação

- Teste funcional com múltiplas páginas e lotes.
- Benchmark sintético em volume crescente.
