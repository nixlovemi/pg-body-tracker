# Falhas de insights observáveis

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** P
- **Dependências:** Nenhuma.

## Evidência e objetivo

`PatientInsightsSnapshotService::snapshotDaily()` incrementa contador de erros, mas descarta a exceção e o cliente afetado.

Investigar falhas de cálculo sem registrar dados clínicos nos logs.

## Arquivos e componentes

`app/Services/PatientInsights/PatientInsightsSnapshotService.php`, `app/Console/Commands/SnapshotPatientInsights.php`, `config/logging.php`.

## Implementação

1. Registrar ID técnico do cliente, sinal, exceção, execução e data; excluir medidas e notas do payload de log.
2. Publicar contagens de avaliados, gravados, ignorados e falhos por execução.
3. Definir alerta para ausência de execução, duração anormal ou taxa de erros acima de limite.
4. Disponibilizar reprocessamento idempotente de um dia ou cliente após correção.
5. Evitar que erro isolado interrompa lote, preservando resultado final não zero quando há falha relevante.

## Critérios de aceite

- [ ] Falhas podem ser atribuídas a execução e cliente.
- [ ] Operação recebe sinal quando rotina deixa de executar ou falha em escala.
- [ ] Reprocessar não cria snapshots duplicados.

## Verificação

- Teste com sinal que lança exceção em um cliente.
- Inspecionar logs sintéticos quanto a dados sensíveis.
