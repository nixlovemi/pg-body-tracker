# Acesso Premium coerente com pagamento

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 05.

## Evidência e objetivo

`User::getPlanType()` guarda decisão por oito horas. O evento `UserPlans::updated()` limpa caches de pagamento, mas não a chave do tipo de plano.

Aplicar mudança de assinatura ao acesso a recursos no momento correto, inclusive cancelamento, expiração e reembolso.

## Arquivos e componentes

`app/Models/User.php`, `app/Models/UserPlans.php`, `app/Helpers/Feature/FeatureAbstract.php`, middleware de plano.

## Implementação

1. Mapear transições de status, data de vigência e efeito esperado em cada recurso Premium.
2. Invalidar cache do usuário ao criar, atualizar ou excluir plano e após conciliação do webhook; avaliar cache curto ou versão de assinatura.
3. Concentrar cálculo de elegibilidade em um serviço único usado por UI, middleware e jobs.
4. Separar cancelamento da renovação do fim do período pago; evitar liberar assinatura pendente.
5. Exibir estado e data de acesso no painel para reduzir chamados.

## Critérios de aceite

- [ ] Pagamento aprovado libera acesso sem esperar oito horas.
- [ ] Expiração e cancelamento respeitam a data contratada.
- [ ] UI e middleware dão a mesma resposta.

## Verificação

- Testes das transições pending/active/paused/canceled/expired.
- Teste com cache previamente aquecido e evento de webhook.
