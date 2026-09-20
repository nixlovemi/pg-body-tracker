# Mapa de dados e terceiros

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** Nenhuma.

## Evidência e objetivo

O código usa QuickChart para gráficos, Mercado Pago, Google Login e analytics; o produto registra medidas, fotos, contatos e pagamentos.

Saber quais dados saem da plataforma e alinhar código, contratos e informação ao usuário.

## Arquivos e componentes

`app/View/Components/ChartPhp.php`, `app/Helpers/Payments/MercadoPago.php`, `app/Http/Controllers/Login.php`, layouts de analytics, `resources/views/site/privacy.blade.php`.

## Implementação

1. Inventariar campos coletados, finalidade, origem, armazenamento, acesso, prazo e destino em cada fluxo.
2. Documentar exatamente que dados gráficos seguem ao QuickChart e se podem ser identificáveis; incluir scripts de analytics e e-mails.
3. Definir critérios técnicos de retenção, exclusão, exportação e revogação de links; incluir backups e logs.
4. Revisar política pública e textos de consentimento com assessoria jurídica a partir do inventário técnico; não tratar código como parecer legal.
5. Usar o mapa como restrição para 07, 17, 29, 30, 31 e 33.

## Critérios de aceite

- [ ] Inventário liga dado, fluxo, terceiro, armazenamento e retenção.
- [ ] Divergências entre comportamento e textos públicos têm plano de correção.
- [ ] Mudanças de integração exigem atualização do inventário.

## Verificação

- Revisão cruzada por produto, engenharia e responsável jurídico.
- Inspecionar payloads sintéticos de integrações.
