# Testes dos fluxos críticos

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 20.

## Evidência e objetivo

Há testes de cálculos, onboarding e check-ins, mas `PdfOptimizationTest` não mede duração nem completa o PDF, e faltam cenários de dois tenants e falhas de pagamento/fila.

Criar uma rede de regressão útil para mudanças em segurança, faturamento e relatórios.

## Arquivos e componentes

`tests/Feature/*`, `tests/Unit/*`, `phpunit.xml`, `.github/workflows/ci-tests.yml`.

## Implementação

1. Organizar matriz de risco: duas contas, papéis, links assinados, criação cruzada, busca, HTML, webhook, plano, PDF e fila.
2. Escrever testes de comportamento que reproduzam cada falha crítica antes da correção correspondente; usar dados sintéticos.
3. Usar fakes somente nas fronteiras externas; exercitar consulta, middleware e persistência reais no banco isolado.
4. Adicionar cenário de PDF completo e estados de erro, sem depender do QuickChart real.
5. Executar conjunto rápido em cada PR e suíte integral na CI; sinalizar teste lento ou instável.

## Critérios de aceite

- [ ] Cada vulnerabilidade P0 tem teste que falha antes da correção e passa depois.
- [ ] Faturamento e PDF têm cenários de sucesso, falha e repetição.
- [ ] CI não depende de internet nem de dados reais.

## Verificação

- Rodar suíte em CI e em ambiente de teste isolado.
- Revisar se testes verificam saída e efeito persistido, não apenas chamadas a métodos.
