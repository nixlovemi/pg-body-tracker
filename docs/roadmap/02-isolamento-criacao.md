# Isolamento na criação de avaliações e metas

- **Prioridade:** P0
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 01.

## Evidência e objetivo

`Avaliation::fHasAccessCustom()` e `Goal::fHasAccessCustom()` só comparam o dono quando `id > 0`; `ModelValidation::addIdField()` confirma existência, não propriedade.

Impedir que gravações vinculem avaliação ou meta ao cliente de outra conta.

## Arquivos e componentes

`app/Models/Avaliation.php`, `app/Models/Goal.php`, `app/Helpers/ModelValidation.php`, `app/Http/Controllers/Avaliation.php`, `app/Http/Controllers/Goal.php`.

## Implementação

1. Criar testes de criação de avaliação e meta com ID de cliente alheio, por formulário e serviço.
2. Resolver cliente em consulta limitada ao profissional antes da persistência; rejeitar ID ausente ou alheio.
3. Validar propriedade também após `fill()` na camada de domínio e ao trocar `client_id` em edição.
4. Revisar demais modelos com chave estrangeira de tenant e preservar fluxos root/console previstos.

## Critérios de aceite

- [ ] Criação e edição cruzadas retornam erro sem alterar linhas.
- [ ] Criação válida do próprio cliente continua funcionando.
- [ ] Operação rejeitada não envia email nem enfileira PDF.

## Verificação

- Teste com duas contas para criar, editar e tentar transferir registros.
- Verificar contagem e `client_id` no banco de teste.
