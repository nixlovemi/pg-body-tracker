# Escape de notas e mensagens

- **Prioridade:** P0
- **Estado:** planejado
- **Esforço relativo:** P
- **Dependências:** Nenhuma.

## Evidência e objetivo

`client-notes.blade.php` usa `{!! nl2br($Avaliation->client_notes) !!}`; alertas exibem mensagens flash e erros sem escape.

Texto inserido por usuários aparece como texto, preservando quebras, sem executar HTML.

## Arquivos e componentes

`resources/views/components/avaliationReport/partials/client-notes.blade.php`, `resources/views/layout/partials/alert-return-messages.blade.php`, `app/Helpers/ModelValidation.php`.

## Implementação

1. Aplicar escape antes de `nl2br` ou componente multiline seguro.
2. Separar mensagens estruturadas de HTML; permitir marcação apenas quando gerada pelo servidor.
3. Inventariar `{!! ... !!}` e classificar fontes: ícone, tradução, usuário ou consulta.
4. Registrar convenção para conteúdo de campo, sessão e tradução interpolada.

## Critérios de aceite

- [ ] Tags digitadas nas notas são exibidas literalmente no web e PDF.
- [ ] Mensagens especiais não executam conteúdo.
- [ ] Ícones estáticos continuam corretos.

## Verificação

- Testes com `<script>`, `<img onerror>` e acentos.
- Inspeção do HTML no relatório, modal e alertas.
