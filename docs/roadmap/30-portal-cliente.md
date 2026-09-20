# Portal leve do cliente

- **Prioridade:** P2
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** 01 e 29.

## Evidência e objetivo

O sistema já possui clientes, metas, avaliações, check-ins e PDFs, mas a experiência do cliente ocorre principalmente por links avulsos.

Aumentar acompanhamento entre consultas sem dar ao cliente poderes de gestão do profissional.

## Arquivos e componentes

`app/Models/Client.php`, `app/Models/User.php`, `app/Http/Controllers/Checkin.php`, `routes/web.php`, novas views do portal.

## Implementação

1. Validar em entrevistas quais duas tarefas geram uso recorrente; iniciar por histórico e resposta a check-in.
2. Definir identidade do cliente e consentimento do profissional para ativação, convite, recuperação e desligamento.
3. Projetar permissões por recurso e tenant: somente dados destinados ao cliente, sem notas privadas ou outros pacientes.
4. Entregar primeira versão responsiva com evolução simples, metas, relatórios compartilhados e check-in.
5. Medir ativação, retorno e impacto nos atendimentos antes de ampliar funcionalidades.

## Critérios de aceite

- [ ] Cliente só vê conteúdo compartilhável do próprio histórico.
- [ ] Profissional pode desativar acesso e revogar sessões.
- [ ] Fluxo funciona sem conta duplicada para a mesma pessoa por profissional, conforme decisão documentada.

## Verificação

- Testes de isolamento profissional/cliente e notas privadas.
- Piloto controlado com profissionais e clientes voluntários.
