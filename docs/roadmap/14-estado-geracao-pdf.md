# Estado de geração visível

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 09.

## Evidência e objetivo

`Avaliation::viewReportPDF()` responde com HTML que atualiza a página a cada três segundos e oferece `sync=1` como saída manual.

Dar feedback claro sem duplicar trabalho nem prender uma requisição longa.

## Arquivos e componentes

`app/Http/Controllers/Avaliation.php`, `app/Services/AvaliationPdfCacheService.php`, `resources/views/components/avaliationReport/partials/download-options.blade.php`.

## Implementação

1. Criar endpoint autorizado de status do snapshot com `pending`, `processing`, `ready` e `failed`.
2. Na tela de relatório, mostrar estado, tempo estimado apenas se houver dados para estimá-lo, e botão de tentar novamente quando permitido.
3. Ao ficar pronto, abrir o arquivo; ao falhar, mostrar mensagem simples e registrar ID de suporte.
4. Evitar polling simultâneo agressivo; encerrar consulta ao sair da página e respeitar intervalos progressivos.
5. Retirar ou restringir `sync=1` depois que a fila estiver confiável.

## Critérios de aceite

- [ ] Usuário entende quando o PDF está na fila, pronto ou falhou.
- [ ] Falha não deixa página recarregando indefinidamente.
- [ ] Acesso ao status de outro profissional é negado.

## Verificação

- Teste de estados e autorização do endpoint.
- Teste manual em rede lenta e no celular.
