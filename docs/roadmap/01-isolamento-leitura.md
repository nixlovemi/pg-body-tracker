# Isolamento de leitura entre contas

- **Prioridade:** P0
- **Estado:** concluído (20/09/2026)
- **Esforço relativo:** M
- **Dependências:** Nenhuma.

## Evidência e objetivo

`BaseModelTrait::getModelByCodedId()` resolve IDs sem escopo de proprietário; ações de `Avaliation` consomem o objeto. Permissão de rota valida papel, não o dono.

Uma conta lê somente clientes, avaliações, metas, PDFs e fotos próprios; links públicos assinados seguem fluxo explícito.

## Arquivos e componentes

`app/Traits/BaseModelTrait.php`, `app/Http/Controllers/Avaliation.php`, `app/Http/Controllers/Goal.php`, `app/Http/Controllers/Client.php`, `routes/web.php`.

## Implementação

1. Inventariar rotas e componentes que recebem `codedId`, `cid`, `cuid` ou nome de foto; registrar dono esperado.
2. Criar consultas com escopo do usuário e política por operação; mapear usos públicos e de console antes de mudar a busca genérica.
3. Aplicar autorização antes de modal, relatório, PDF, envio por link ou foto; responder 404 para registro alheio.
4. Definir identidade necessária aos jobs sem depender da sessão web.

## Critérios de aceite

- [x] Conta A não obtém conteúdo da conta B por ID direto, inclusive JSON e PDF em cache.
- [x] Links públicos abrem apenas o recurso autorizado e dentro da validade.
- [x] Listagens mantêm o filtro correto.

## Verificação

- Teste de integração com dois profissionais e IDs trocados em todas as rotas de leitura.
- Testar link assinado válido, vencido e adulterado.

## Resultado

Clientes e avaliações agora são buscados com filtro de proprietário nas rotas autenticadas; modais, fotos, check-in, metas e relatórios retornam 404 para IDs de outra conta. A rota pública do PDF conserva sua assinatura temporária e resolve a avaliação sem sessão. O job de cache recebe o ID da avaliação, sem depender da sessão web. As listagens existentes continuam filtradas pelo usuário na consulta.

`TenantReadIsolationTest` passou com SQLite em memória: 5 testes, 50 asserções. O teste cobre duas contas, root, ID direto, resposta JSON, PDF em cache, fotos e assinatura válida, ausente, adulterada ou vencida. A resolução e autorização agora são compartilhadas com o middleware descrito em `docs/architecture/authorization.md`. O fluxo de alteração/vinculação de cliente continua no item 02.
