# Autorização centralizada

- **Prioridade:** P3
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 01, 02 e 21.

## Evidência e objetivo

Permissões por papel estão em `Permissions`, checagens de dono aparecem em controllers, modelos e `BaseModelTrait`; a distribuição dificulta auditoria.

Ter uma fonte explícita de regra por recurso e operação sem perder proteção em jobs e links assinados.

## Arquivos e componentes

`app/Helpers/Permissions.php`, `app/Traits/BaseModelTrait.php`, `app/Providers/AuthServiceProvider.php`, controllers e policies novas.

## Implementação

1. Catalogar atores: root, profissional, cliente, destinatário de link e processo de sistema; listar ações permitidas.
2. Implementar policies ou serviço de autorização para ver, criar, editar, excluir, exportar e compartilhar cada recurso.
3. Trocar consultas e controllers gradualmente para regras centrais; manter filtros de tenant no banco para reduzir exposição acidental.
4. Separar autorização de usuário web da capacidade específica de link assinado; registrar validade e escopo.
5. Remover permissões duplicadas após cobertura de testes e documentar exceções administrativas.

## Critérios de aceite

- [ ] Matriz de atores e ações está documentada e testada.
- [ ] Controller não decide propriedade por comparação ad hoc.
- [ ] Jobs e links públicos usam contexto explícito e mínimo.

## Verificação

- Testes de matriz por papel, tenant e operação.
- Revisão de todas as rotas após migração.
