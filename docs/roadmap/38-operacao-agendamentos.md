# Operação e agendamentos padronizados

- **Prioridade:** P3
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 09, 23 e 24.

## Evidência e objetivo

`app/Console/Kernel.php` não agenda tarefas; scripts separados disparam PDF, check-ins, engajamento e snapshots.

Tornar rotina, fila e recuperação reproduzíveis em produção sem depender de conhecimento tácito.

## Arquivos e componentes

`app/Console/Kernel.php`, `app/Console/Commands/*`, `scripts/*`, `config/queue.php`, arquivos de deploy.

## Implementação

1. Inventariar scripts, frequência, duração, entradas e dono operacional de cada rotina.
2. Escolher scheduler único ou manter agendador externo com manifesto versionado; impedir sobreposição e duplicação.
3. Definir processos permanentes de fila, reinício após deploy, alertas de jobs falhos e retenção dos registros.
4. Criar runbook de backup, restauração, reprocessamento, rotação de segredo e incidente de dados.
5. Expor health check operacional que confirme última execução e idade da fila sem revelar dados de pacientes.

## Critérios de aceite

- [ ] Todas as rotinas têm frequência, responsável, alerta e reprocessamento documentados.
- [ ] Deploy reinicia workers e não interrompe jobs de forma silenciosa.
- [ ] Restauração de backup foi ensaiada em ambiente isolado.

## Verificação

- Simular parada do worker e atraso de rotina em homologação.
- Executar ensaio de restauração e registrar tempo/resultado.
