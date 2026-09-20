# Banco de testes isolado

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** P
- **Dependências:** Nenhuma.

## Evidência e objetivo

`phpunit.xml` não define conexão ou banco de teste; a CI injeta um banco próprio. Localmente, testes com `RefreshDatabase` podem depender de `.env`.

Impedir que testes e `migrate:fresh` atinjam dados não descartáveis.

## Arquivos e componentes

`phpunit.xml`, `.env.example`, `tests/TestCase.php`, `.github/workflows/ci-tests.yml`.

## Implementação

1. Criar configuração de teste independente com nome de banco explicitamente reservado a testes e credenciais de privilégio mínimo.
2. Adicionar guardrail no bootstrap de testes: falhar se conexão, banco ou host corresponder a produção/desenvolvimento compartilhado.
3. Fornecer comando documentado para criar/resetar somente o banco de teste e instruções para CI.
4. Evitar copiar `.env` de uso real para testes sem substituição validada.
5. Revisar factories/seeders para que não usem serviços externos em teste.

## Critérios de aceite

- [ ] Suíte recusa rodar em banco não autorizado.
- [ ] CI e máquina local usam bancos descartáveis explícitos.
- [ ] Nenhum teste envia e-mail ou chama pagamento real.

## Verificação

- Testar guardrail com configurações de teste e não teste.
- Executar suíte apenas após conferir o nome do banco.
