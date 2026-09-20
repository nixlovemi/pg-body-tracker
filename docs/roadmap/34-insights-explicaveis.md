# Insights explicáveis

- **Prioridade:** P2
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 24 e 25.

## Evidência e objetivo

`PatientSignalEngine` agrega sinais e o card apresenta status/percentual; as regras usam dados de avaliação, meta e check-in.

Permitir que o profissional entenda cada alerta, sua confiança e a ação sugerida.

## Arquivos e componentes

`app/Services/PatientInsights/*`, `app/Presenters/ClientInsightsCardPresenter.php`, `resources/views/app/client/register.blade.php`.

## Implementação

1. Definir linguagem dos sinais com nutricionistas e educadores físicos; evitar impressão de diagnóstico.
2. Mostrar dados de origem, janela temporal, última atualização e motivo de falta de confiança.
3. Ordenar no máximo três motivos relevantes e associar ação possível: revisar meta, contato ou reavaliação.
4. Permitir marcar alerta como visto e capturar feedback sobre utilidade antes de automatizar intervenções.
5. Versionar regras e limiares para comparar comportamento ao longo do tempo.

## Critérios de aceite

- [ ] Cada status exibido possui motivo e data rastreáveis.
- [ ] Baixa confiança ou dados ausentes não aparecem como risco preciso.
- [ ] Ação sugerida respeita permissão e plano do profissional.

## Verificação

- Testes de sinais contraditórios, poucos dados e regra alterada.
- Revisão de textos por profissionais do domínio.
