# Menor exposição em respostas e logs

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 04 e 05.

## Evidência e objetivo

`User::$hidden` não inclui `password`; o webhook registra payload integral e exceções com detalhes. Existem dados sensíveis em pacientes, fotos e pagamentos.

Retornar e registrar apenas o mínimo necessário para operar e investigar incidentes.

## Arquivos e componentes

`app/Models/User.php`, `app/Http/Controllers/Subscription.php`, `app/Helpers/Payments/PaymentGatewayAbstract.php`, `config/logging.php`.

## Implementação

1. Inventariar serialização de User, Client, Avaliation e planos em API, JSON de modal, logs e e-mails.
2. Ocultar hash da senha e dados de autenticação no modelo e preferir DTOs explícitos para respostas públicas.
3. Criar política de redaction para webhook, exceções e logs de suporte, preservando IDs técnicos de correlação.
4. Definir prazo e controle de acesso de logs e rotacioná-los; documentar o procedimento de acesso.
5. Validar que alterações de ocultação não quebrem autenticação nem campos usados internamente.

## Critérios de aceite

- [ ] Nenhuma resposta HTTP inclui hash de senha ou token interno.
- [ ] Logs de pagamento e PDF não contêm medidas, fotos ou payload bruto dispensável.
- [ ] Incidente ainda pode ser rastreado por ID técnico.

## Verificação

- Testes de serialização e snapshots de resposta.
- Amostragem de logs sintéticos após mudanças.
