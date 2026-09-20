# Escape de notas e mensagens

- **Prioridade:** P0
- **Estado:** concluído
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

- [x] Tags digitadas nas notas são exibidas literalmente no web e PDF.
- [x] Mensagens especiais não executam conteúdo.
- [x] Ícones estáticos continuam corretos.

## Verificação

- Testes com `<script>`, `<img onerror>` e acentos.
- Inspeção do HTML no relatório, modal e alertas.

## Convenção e inventário de saída HTML

- Campos de usuário, mensagens de sessão, erros de validação e traduções interpoladas com configuração devem ser escapados ao renderizar. Para texto com várias linhas, usar `nl2br(e($texto))`.
- `ModelValidation` fornece mensagens em texto com quebras de linha e a lista estruturada em `errors`; componentes decidem como apresentar as linhas.
- HTML gerado pelo servidor permanece explícito: ícones estáticos, componentes de gráfico/progresso, traduções estáticas com marcação e a tabela de relatório. Seus valores variáveis agora são escapados na composição.
- Nos gráficos, apenas os marcadores coloridos criados por `getTableRowLabel()` são `HtmlString`; títulos e dados comuns continuam escapados. Isso preserva a tabela no relatório web e no PDF.
- O valor de gênero inserido em JavaScript no modal usa serialização JSON do Blade.
- Verificação: `HtmlOutputSafetyTest` cobre nota compartilhada entre web/PDF, alertas e mensagens de validação. `git diff --check` e lint PHP passaram.
