# Fila e falhas do PDF

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 06.

## Evidência e objetivo

`GenerateAvaliationPdfCacheJob` tem timeout de 180 s e `queue.php` usa `retry_after` de 90 s; falhas no serviço retornam `null` e mantêm estado pendente.

Garantir um único processamento por snapshot, erro visível e tentativa controlada.

## Arquivos e componentes

`app/Jobs/GenerateAvaliationPdfCacheJob.php`, `app/Services/AvaliationPdfCacheService.php`, `app/Models/AvaliationPdfCache.php`, `config/queue.php`, scripts de fila.

## Implementação

1. Definir conexão e trabalhador em produção; configurar `retry_after` maior que timeout com margem e revisar número de tentativas.
2. Usar identificador único que inclua hash do snapshot e variante, para não bloquear versões novas da mesma avaliação.
3. Persistir estados `pending`, `processing`, `ready`, `failed`, timestamps, tentativas e mensagem técnica limitada.
4. Relançar exceções recuperáveis ao job; marcar falha final e oferecer nova tentativa sem loop infinito.
5. Monitorar jobs falhos, duração e idade máxima da fila; documentar reinício do trabalhador.

## Critérios de aceite

- [ ] Job longo não é reclamado simultaneamente pela fila.
- [ ] Falha chega a `failed` e pode ser tentada novamente.
- [ ] Duas solicitações do mesmo snapshot não geram arquivos concorrentes.

## Verificação

- Testes com falha simulada do renderizador e reentrega da fila.
- Verificar operação do trabalhador em ambiente de homologação.
