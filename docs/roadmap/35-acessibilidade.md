# Acessibilidade dos fluxos

- **Prioridade:** P2
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 27.

## Evidência e objetivo

Fluxos de avaliação usam modais extensos, ícones e controles com `javascript:;`; o site usa ícones para diferenciar recursos dos planos.

Permitir concluir cadastro, avaliação, relatório e pagamento por teclado e tecnologia assistiva.

## Arquivos e componentes

`resources/views/app/avaliation/modalRegister.blade.php`, `resources/views/layout/dashboard.blade.php`, views de cadastro, relatório e assinatura.

## Implementação

1. Auditar páginas críticas com teclado, leitor de tela e contraste em desktop/celular.
2. Usar botões para ações e links para navegação; adicionar rótulos, descrições e estado aos ícones e controles.
3. Corrigir foco inicial e retorno ao fechar modais, ordem de tabulação e anúncio de erro por campo.
4. Revisar tabela, gráficos e PDF para alternativas textuais e leitura em ordem lógica.
5. Incluir checklist manual e verificações automatizadas de acessibilidade no fluxo de revisão.

## Critérios de aceite

- [ ] Tarefas críticas são concluídas sem mouse.
- [ ] Erros são identificados e anunciados no campo correto.
- [ ] Diferenças dos planos não dependem apenas de cor/ícone.

## Verificação

- Teste manual com teclado e leitor de tela em fluxos completos.
- Automação para problemas estruturais repetíveis.
