# Separação do modelo de avaliação

- **Prioridade:** P3
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** 21 e 25.

## Evidência e objetivo

`app/Models/Avaliation.php` concentra persistência, validação, fórmulas, fotos, e-mail e eventos; o controller também acumula preparação de formulários e links.

Permitir evolução de cálculo e relatório com mudanças pequenas e comportamento preservado.

## Arquivos e componentes

`app/Models/Avaliation.php`, `app/Http/Controllers/Avaliation.php`, `app/Helpers/Avaliation/*`, `app/Services/*`.

## Implementação

1. Mapear métodos e dependências do modelo por responsabilidade: cálculo, validação, mídia, notificação e ciclo de vida.
2. Criar serviços ou objetos de valor para cálculos puros, mantendo uma fachada compatível no modelo durante transição.
3. Mover validação de requisição e autorização para camadas próprias; separar eventos de efeitos externos como e-mail e PDF.
4. Migrar um grupo de indicadores por vez com casos de referência 25 e testes 21.
5. Após uso estável, remover delegações antigas e documentar limites entre domínio, persistência e apresentação.

## Critérios de aceite

- [ ] Resultados existentes permanecem iguais durante extração, salvo correção aprovada.
- [ ] Cálculos puros podem ser testados sem banco e autenticação.
- [ ] Modelo deixa de disparar integração externa de forma implícita.

## Verificação

- Testes de caracterização antes/depois por grupo de métodos.
- Revisão de dependências para evitar ciclos entre serviços.
