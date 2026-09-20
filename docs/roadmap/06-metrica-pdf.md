# Medição do fluxo PDF

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** Nenhuma.

## Evidência e objetivo

`AvaliationPdfCacheService` concentra preparação, renderização e armazenamento sem tempos por etapa.

Estabelecer linha de base antes de otimizar e acompanhar p50, p95, taxa de erro e acerto de cache.

## Arquivos e componentes

`app/Services/AvaliationPdfCacheService.php`, `app/Jobs/GenerateAvaliationPdfCacheJob.php`, `app/View/Components/ChartPhp.php`, `config/logging.php`.

## Implementação

1. Definir cenários sintéticos: primeira avaliação, dez avaliações históricas, quatro fotos e opções de gráficos/fotos ligadas e desligadas.
2. Medir espera na fila, consultas, preparação de cards, gráficos QuickChart, fotos, Blade, DomPDF e escrita em disco com um identificador de geração.
3. Registrar duração, tamanho e número de páginas sem nome, foto ou medidas de paciente nos logs.
4. Comparar cache frio e quente; coletar amostra em ambiente semelhante à produção antes de fixar meta de tempo.

## Critérios de aceite

- [ ] Relatório por etapa indica onde o tempo é gasto.
- [ ] p50, p95, erro e cache hit possuem definição e painel/consulta.
- [ ] Nenhum log contém dados corporais identificáveis.

## Verificação

- Rodar os cenários sintéticos e registrar linha de base no plano.
- Conferir que logs não incluem dados pessoais.
