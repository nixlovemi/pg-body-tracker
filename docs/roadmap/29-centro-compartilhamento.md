# Centro de compartilhamento

- **Prioridade:** P2
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** 01 e 18.

## Evidência e objetivo

O sistema cria URLs curtas para PDF/check-in; `UrlShort` armazena destino, e ações de envio ficam nos modais da avaliação.

Dar ao profissional controle sobre links enviados e ao cliente uma abertura previsível e segura.

## Arquivos e componentes

`app/Models/UrlShort.php`, `app/Http/Controllers/UrlShortController.php`, `app/Http/Controllers/Avaliation.php`, nova tabela de compartilhamentos.

## Implementação

1. Criar modelo de compartilhamento vinculado ao dono, avaliação, destinatário opcional, escopo, expiração e revogação.
2. Registrar links emitidos por e-mail/WhatsApp sem armazenar conteúdo sensível desnecessário; migrar URLs curtas existentes conforme viabilidade.
3. Checar revogação e validade antes de resolver link assinado ou entregar PDF; manter 404/410 sem revelar o recurso.
4. Oferecer tela para copiar, revogar, reenviar e ver estado de cada link.
5. Definir política de limpeza de URL curta e proteção contra tentativa massiva de chaves.

## Critérios de aceite

- [ ] Profissional vê e revoga apenas seus links.
- [ ] Link revogado ou vencido não abre PDF mesmo se URL curta existir.
- [ ] Compartilhamento não expõe notas privadas ou fotos excluídas por opção.

## Verificação

- Testes de dono, validade, revogação e escopo.
- Teste de jornada em celular e e-mail.
