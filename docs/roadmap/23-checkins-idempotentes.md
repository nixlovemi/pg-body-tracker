# Check-ins sem envio duplicado

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 21.

## Evidência e objetivo

`CheckinDispatchService::dispatchForConfig()` envia e-mail antes de gravar a data; duas execuções podem selecionar o mesmo ciclo.

Enviar no máximo uma mensagem por ciclo/reminder, com recuperação após falhas.

## Arquivos e componentes

`app/Services/Checkin/CheckinDispatchService.php`, `app/Models/CheckinConfig.php`, `app/Http/Controllers/Checkin.php`, nova migração de tentativas.

## Implementação

1. Criar identificador de ciclo e tentativa de envio com chave única por configuração, data e tipo.
2. Reservar a tentativa em transação antes de enviar; usar outbox/job para entregar e marcar sucesso/falha.
3. Definir política para falha de e-mail, reenvio, expiração e resposta recebida durante o envio.
4. Remover dependência de login temporário para persistir dados em comando, usando serviço de domínio com escopo explícito.
5. Revisar `sendNow` e o disparo agendado para usarem a mesma regra.

## Critérios de aceite

- [ ] Execuções simultâneas não duplicam e-mail.
- [ ] Falha de entrega pode ser retomada sem perder ciclo.
- [ ] Resposta do cliente interrompe lembrete pendente.

## Verificação

- Teste concorrente com duas execuções e falha de transporte.
- Teste dos limites de lembrete existentes.
