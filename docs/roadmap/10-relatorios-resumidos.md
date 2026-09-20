# Modelos resumido, comparativo e técnico

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** 06.

## Evidência e objetivo

O template em `components/avaliationReport` monta cards, gráficos, fotos e notas no mesmo relatório, com quebras de página repetidas.

Oferecer documento adequado à conversa com o cliente, com variante rápida e variante detalhada.

## Arquivos e componentes

`app/Presenters/AvaliationReportPresenter.php`, `resources/views/components/avaliationReport/*`, `app/Services/AvaliationPdfCacheService.php`.

## Implementação

1. Definir com 3–5 profissionais o conteúdo obrigatório de resumo, comparativo e técnico; manter unidades e método de medição visíveis.
2. Criar uma representação de dados de relatório independente do Blade, com seções selecionáveis e ordem previsível.
3. Implementar resumo com poucos indicadores e um gráfico opcional; comparativo com datas e deltas; técnico com dados completos.
4. Expor escolha antes de gerar e incluir o modelo no hash/cache e no nome do arquivo.
5. Conferir conteúdo nos planos Free/Premium sem alterar direitos comerciais por acidente.

## Critérios de aceite

- [ ] Os três modelos têm propósito e conteúdo documentados.
- [ ] Dados e unidades batem entre tela, PDF e CSV quando aplicável.
- [ ] Resumo tem tamanho e tempo medidos contra o relatório atual.

## Verificação

- Revisão visual de PDFs com dados incompletos, fotos e histórico.
- Teste de seleção de modelo e cache por variante.
