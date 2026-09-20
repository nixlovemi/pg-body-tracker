# Plano de evolução do PG BodyTracker

Este diretório transforma a revisão arquitetural de 20/09/2026 em 39 entregas independentes. Cada número possui um arquivo com contexto, desenho da solução, critérios de aceite e verificação. A ordem é uma proposta de execução; dependências técnicas e evidências novas podem alterar a prioridade.

## Como usar

1. Escolher o próximo item **pronto** na tabela, confirmar seu escopo com a situação atual do código e registrar decisões ainda abertas no próprio arquivo.
2. Implementar em mudança pequena e revisável. Em itens de segurança, adicionar primeiro um teste que demonstre o problema sem acessar dados reais.
3. Executar a verificação descrita no arquivo em ambiente isolado. Registrar o resultado, riscos residuais e a data; mudar o estado para `concluído` somente após os critérios de aceite.
4. Reavaliar os dependentes e a ordem após cada entrega. Itens de produto exigem validação com profissionais antes da construção completa.

**Estados:** `planejado`, `em andamento`, `bloqueado`, `concluído`. **Esforço relativo:** P = mudança localizada; M = vários componentes; G = mudança transversal ou migração. Não são estimativas de prazo. **Evidência:** achados de código são observações estáticas; hipóteses de performance, UX e negócio precisam de medição.

## Sequência e dependências

| Nº | Prioridade | Entrega | Dependências principais |
| --- | --- | --- | --- |
| [01](01-isolamento-leitura.md) | P0 | Isolamento de leitura entre contas | — |
| [02](02-isolamento-criacao.md) | P0 | Isolamento na criação de avaliações e metas | 01 |
| [03](03-busca-sql-segura.md) | P0 | Busca e ordenação SQL seguras | — |
| [04](04-saida-html-segura.md) | P0 | Escape de notas e mensagens | — |
| [05](05-webhook-autentico-idempotente.md) | P0 | Webhook autêntico e idempotente | — |
| [06](06-metrica-pdf.md) | P1 | Medição do fluxo PDF | — |
| [07](07-graficos-pdf-locais.md) | P1 | Gráficos fora do caminho externo crítico | 06, 26 |
| [08](08-invalidação-cache-pdf.md) | P1 | Invalidação correta do PDF | 06 |
| [09](09-fila-pdf-confiavel.md) | P1 | Fila e falhas do PDF | 06 |
| [10](10-relatorios-resumidos.md) | P1 | Modelos resumido, comparativo e técnico | 06 |
| [11](11-consultas-graficos.md) | P1 | Consultas eficientes nos gráficos | 06 |
| [12](12-imagens-css-pdf.md) | P1 | Imagens e CSS de impressão | 06 |
| [13](13-cache-graficos-warmup.md) | P1 | Cache dos gráficos e aquecimento | 07 |
| [14](14-estado-geracao-pdf.md) | P1 | Estado de geração visível | 09 |
| [15](15-cache-plano-premium.md) | P1 | Acesso Premium coerente com pagamento | 05 |
| [16](16-limite-login.md) | P1 | Proteção de login e recuperação | — |
| [17](17-dados-respostas-logs.md) | P1 | Menor exposição em respostas e logs | 04, 05 |
| [18](18-fotos-privadas.md) | P1 | Fotos privadas e uploads validados | 01 |
| [19](19-atualizacao-plataforma.md) | P1 | Atualização da plataforma | 20, 21 |
| [20](20-banco-testes-isolado.md) | P1 | Banco de testes isolado | — |
| [21](21-testes-regressao-criticos.md) | P1 | Testes dos fluxos críticos | 20 |
| [22](22-relatorios-e-envios-lotes.md) | P1 | Relatórios e envios em lotes | 06 |
| [23](23-checkins-idempotentes.md) | P1 | Check-ins sem envio duplicado | 21 |
| [24](24-observabilidade-insights.md) | P1 | Falhas de insights observáveis | — |
| [25](25-validacao-indicadores.md) | P1 | Precisão dos indicadores | 21 |
| [26](26-mapa-dados-terceiros.md) | P1 | Mapa de dados e terceiros | — |
| [27](27-avaliacao-mobile.md) | P2 | Avaliação móvel mais curta | 33 |
| [28](28-resumo-para-cliente.md) | P2 | Resultados em linguagem simples | 10, 25 |
| [29](29-centro-compartilhamento.md) | P2 | Centro de compartilhamento | 01, 18 |
| [30](30-portal-cliente.md) | P2 | Portal leve do cliente | 01, 29 |
| [31](31-importacao-exportacao.md) | P2 | Entrada e saída de dados | 01, 20 |
| [32](32-planos-claros.md) | P2 | Planos mais claros | 15 |
| [33](33-funil-adocao.md) | P2 | Medição da adoção | 26 |
| [34](34-insights-explicaveis.md) | P2 | Insights explicáveis | 24, 25 |
| [35](35-acessibilidade.md) | P2 | Acessibilidade dos fluxos | 27 |
| [36](36-separacao-modelo-avaliacao.md) | P3 | Separação do modelo de avaliação | 21, 25 |
| [37](37-autorizacao-centralizada.md) | P1 | Autorização centralizada (em andamento) | 01, 02, 21 |
| [38](38-operacao-agendamentos.md) | P3 | Operação e agendamentos padronizados | 09, 23, 24 |
| [39](39-limpeza-codigo-config.md) | P3 | Limpeza de código e configuração | 19, 36, 38 |

## Marcos recomendados

- **M1, segurança de dados:** 01–05; aproveitar 20 e 21 desde o início para testes seguros.
- **M2, PDF previsível:** 06–14. Fazer a medição 06 antes de apostar em troca de renderizador; repetir o cenário de referência a cada melhoria.
- **M3, operação confiável:** 15–26, com atualização de plataforma 19 planejada como migração própria.
- **M4, adoção e produto:** 27–35, após entrevistas e dados do funil. Os itens 36–39 podem acompanhar alterações relacionadas para evitar uma reescrita ampla.

## Critério geral de pronto

O comportamento novo está coberto por verificação proporcional ao risco; erros e permissões são tratados; há plano de ativação e reversão quando muda dado, fila ou integração; documentação de operação foi ajustada; nenhuma informação real de pacientes foi usada em testes ou exemplos.
