# Cache dos gráficos e aquecimento

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** P
- **Dependências:** 07.

## Evidência e objetivo

`ChartPhp::getCacheKey()` usa só configuração, embora largura/altura afetem o resultado. `AvaliationPdfWarmupService` não tem chamada encontrada e usa tamanho padrão diferente do PDF.

Eliminar colisões de cache e manter apenas aquecimento com ganho demonstrado.

## Arquivos e componentes

`app/View/Components/ChartPhp.php`, `app/Services/AvaliationPdfWarmupService.php`, `resources/views/components/avaliation-graph-abstract.blade.php`.

## Implementação

1. Adicionar dimensões, formato, versão do renderizador e configuração normalizada à chave.
2. Medir hit rate e custo do cache; evitar armazenar grandes Base64 sem limite ou prazo adequado.
3. Decidir após 07 se warmup reduz tempo percebido; integrá-lo ao job com dimensões reais ou remover serviço morto.
4. Versionar chave ao mudar renderizador para não servir imagem antiga.

## Critérios de aceite

- [ ] Mesmo gráfico em tamanhos diferentes gera imagens próprias.
- [ ] Cache hit conserva qualidade e reduz tempo medido.
- [ ] Não resta serviço de aquecimento sem uso.

## Verificação

- Teste de chaves diferentes para duas dimensões.
- Comparar tempo de primeira e segunda geração.
