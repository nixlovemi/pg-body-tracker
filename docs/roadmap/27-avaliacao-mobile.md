# Avaliação móvel mais curta

- **Prioridade:** P2
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** 33.

## Evidência e objetivo

`modalRegister.blade.php` reúne muitas etapas e campos; já existe um fluxo rápido que oculta alguns grupos.

Concluir avaliações durante atendimento em celular com menos erros e interrupções.

## Arquivos e componentes

`resources/views/app/avaliation/modalRegister.blade.php`, `app/Http/Controllers/Avaliation.php`, CSS e JS do formulário.

## Implementação

1. Observar 5 profissionais preenchendo avaliação por método em celular; medir conclusão, tempo, campos difíceis e abandono.
2. Definir roteiro curto por método, com progressão visível, unidades ao lado dos campos e cálculo mostrado na revisão.
3. Projetar rascunho seguro com retomada, preservando fotos e dados parciais sob autorização do profissional.
4. Separar campos avançados sem ocultar entradas necessárias; validar no servidor independentemente da UI.
5. Migrar por etapas com opção de voltar ao fluxo anterior até o novo ser estável.

## Critérios de aceite

- [ ] Fluxo rápido permite concluir os três métodos existentes.
- [ ] Rascunho só é visto pelo próprio profissional e pode ser descartado.
- [ ] Tempo, erros e abandono são medidos antes/depois.

## Verificação

- Teste de usabilidade no celular com dados fictícios.
- Testes de validação, rascunho e retomada.
