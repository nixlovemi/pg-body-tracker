# Webhook autêntico e idempotente

- **Prioridade:** P0
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** Nenhuma.

## Evidência e objetivo

`Subscription::mercadoPagoWebhook()` passa o corpo ao processamento sem assinatura; a deduplicação atual usa ação e data.

Processar só eventos autênticos, uma vez por ID, mantendo plano coerente após falhas e reentregas.

## Arquivos e componentes

`app/Http/Controllers/Subscription.php`, `app/Helpers/Payments/PaymentGatewayAbstract.php`, `app/Helpers/Payments/MercadoPago.php`, nova migração de eventos.

## Implementação

1. Definir a variante de notificação utilizada e validar assinatura com cabeçalhos, corpo bruto e segredo.
2. Persistir ID, tipo, recurso e estado do evento com índice único.
3. Responder rápido e conciliar em job, consultando o estado no provedor antes de mudar acesso.
4. Reduzir logs do payload aos campos operacionais necessários.
5. Criar reprocessamento por ID e testes de duplicação, ordem invertida e assinatura inválida.

## Critérios de aceite

- [ ] Assinatura inválida não altera plano.
- [ ] Reentrega produz um único efeito de negócio.
- [ ] Falha transitória pode ser reprocessada com rastreabilidade.

## Verificação

- Testes com assinatura válida/inválida, duplicatas e ordem invertida.
- Homologar no Mercado Pago antes de produção.
