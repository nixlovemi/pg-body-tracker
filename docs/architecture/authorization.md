# Autorização por recurso

## Caminho de uma rota autenticada

1. `authWeb` confirma a permissão de papel da rota.
2. `tenant.resource` lê o ID codificado do parâmetro de rota, query string ou corpo da requisição.
3. `TenantResourceResolver` valida o ID e consulta o modelo com `visibleTo($user)`; registros de outra conta não entram no resultado.
4. A policy do modelo confirma a operação (`view`, `update`, `delete` ou `share`). O middleware entrega o modelo em `$request->attributes`, sob a chave `tenant.<recurso>`.

Exemplo: `tenant.resource:avaliation,codedId,view` entrega a avaliação em `tenant.avaliation`. O quarto argumento permite uma chave diferente quando a mesma requisição usa dois clientes, como a cópia da configuração de check-in.

O resolvedor aceita somente os recursos da sua lista explícita. Adicionar um recurso exige um scope `visibleTo`, uma policy registrada em `AuthServiceProvider` e testes com duas contas.

## Atores e operações

| Recurso | Profissional dono | Profissional de outra conta | Root | Link público assinado |
| --- | --- | --- | --- | --- |
| Cliente | Ver e editar | 404 | Ver e editar | Sem acesso |
| Avaliação | Ver, editar e compartilhar | 404 | Ver, editar e compartilhar | Abrir somente o PDF da avaliação indicada |
| Foto de avaliação | Ver | 404 | Ver | Incluída no PDF autorizado |
| Meta | Ver e excluir | 404 | Ver e excluir | Sem acesso |
| Configuração de check-in | Ver e editar | 404 | Ver e editar | Formulário específico protegido por assinatura |
| Plano do usuário | Ver e atualizar | 404 | Ver e atualizar | Sem acesso |

As permissões de papel continuam em `Permissions`; as policies decidem a propriedade do registro. O resultado 404 não revela se um ID existe em outra conta.

## Contextos sem sessão do profissional

`showMyAvaliation` usa o middleware `signed` do Laravel e resolve o ID da avaliação indicado na URL. O cliente que recebeu o link não precisa de sessão do profissional. Os formulários públicos de check-in seguem o mesmo princípio, com sua própria assinatura e regras de validade.

O job de cache do PDF recebe o ID da avaliação e a carrega explicitamente. Ele não usa o usuário da sessão. A autorização ocorre na rota antes de devolver um PDF em cache ao profissional; o link público depende da assinatura.

## Trabalho posterior

O ciclo de criação e alteração dos modelos ainda utiliza `BaseModelTrait::fHasAccessCustom`. A centralização dessas regras e a validação do vínculo entre cliente, avaliação e meta fazem parte do item 02 do roadmap. Ao ampliar a arquitetura, mantenha as consultas filtradas por conta e teste primeiro acesso próprio, acesso cruzado, root e links públicos.
