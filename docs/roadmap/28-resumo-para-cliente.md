# Resultados em linguagem simples

- **Prioridade:** P2
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 10 e 25.

## Evidência e objetivo

O relatório contém muitos indicadores e gráficos; o site promete evolução compreensível e motivação do cliente.

Apresentar mudanças relevantes com contexto, sem dar interpretação clínica automática indevida.

## Arquivos e componentes

`app/Presenters/AvaliationReportPresenter.php`, `resources/views/components/avaliationReport/*`, `app/Models/Avaliation.php`.

## Implementação

1. Entrevistar profissionais e clientes para escolher até três mudanças que sempre merecem destaque.
2. Criar camada de resumo com valor atual, anterior, unidade, método e data; só mostrar delta quando comparável.
3. Permitir comentário revisado pelo profissional e meta relacionada; distinguir dado medido de estimativa.
4. Adicionar aviso de dados insuficientes e texto neutro quando não houver comparação confiável.
5. Usar o mesmo resumo no PDF compacto e na visualização web.

## Critérios de aceite

- [ ] Resumo não inventa comparação sem histórico ou método compatível.
- [ ] Profissional pode revisar texto antes de compartilhar.
- [ ] Valores conferem com indicadores completos.

## Verificação

- Revisão de exemplos com profissional de domínio.
- Testes de dados ausentes, métodos diferentes e metas.
