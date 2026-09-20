# Entrada e saída de dados

- **Prioridade:** P2
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** 01 e 20.

## Evidência e objetivo

Cadastro e avaliação hoje são formulários; relatórios têm CSV, mas não existe fluxo geral de migração de planilhas no código revisado.

Reduzir custo de adoção e permitir que o profissional retire seus próprios dados de modo organizado.

## Arquivos e componentes

`app/Models/Client.php`, `app/Models/Avaliation.php`, `app/Models/Goal.php`, novos jobs e telas de importação/exportação.

## Implementação

1. Definir formatos CSV/XLSX prioritários com amostras anonimizadas; separar importação de clientes, avaliações e metas.
2. Criar prévia de mapeamento de colunas, unidade, data, duplicatas e erros antes de gravar.
3. Processar em lotes idempotentes com transação por linha ou grupo e relatório de rejeições; vincular tudo ao tenant atual.
4. Criar exportação completa autenticada em job, com arquivo temporário privado e validade definida.
5. Documentar tratamento de fotos, links e dados incompletos; nunca executar fórmula de planilha recebida.

## Critérios de aceite

- [ ] Importação mostra prévia e não grava registros de outra conta.
- [ ] Repetir arquivo não duplica linhas sem confirmação.
- [ ] Exportação contém dados próprios e pode ser baixada só pelo titular.

## Verificação

- Testes com datas, decimais brasileiros, duplicatas e erro no meio do lote.
- Piloto com duas planilhas reais anonimizadas.
