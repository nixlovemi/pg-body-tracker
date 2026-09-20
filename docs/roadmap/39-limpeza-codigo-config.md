# Limpeza de código e configuração

- **Prioridade:** P3
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 19, 36 e 38.

## Evidência e objetivo

Há serviço de aquecimento de PDF sem chamada encontrada, comentários antigos e nomes legados em `.env.example`, Docker e documentação.

Reduzir ambiguidades e manutenção acidental após estabilizar os fluxos principais.

## Arquivos e componentes

`README.md`, `.env.example`, `Dockerfile`, `docker-compose.yml`, `app/Services/AvaliationPdfWarmupService.php`.

## Implementação

1. Inventariar código não referenciado e configuração divergente entre desenvolvimento, CI e produção; confirmar antes de remover.
2. Remover caminhos mortos em mudanças pequenas, cada uma acompanhada de teste ou busca de referências.
3. Padronizar nomes de variáveis, caminhos de scripts e documentação de setup; manter migração de configuração com período de compatibilidade.
4. Revisar warnings de análise estática e formatação; adotar verificações leves na CI.
5. Atualizar README com arquitetura atual e links para este roadmap e runbooks.

## Critérios de aceite

- [ ] Não resta configuração documentada que a aplicação ignora.
- [ ] Setup limpo reproduz aplicação, fila e testes.
- [ ] Remoções não quebram fluxos críticos.

## Verificação

- Busca de referências e execução de CI após cada lote.
- Instalação de teste a partir do README atualizado.
