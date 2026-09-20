# Medição da adoção

- **Prioridade:** P2
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 26.

## Evidência e objetivo

O site já usa analytics; onboarding existe, mas não há no código revisado uma visão única da jornada até o primeiro PDF e upgrade.

Descobrir onde profissionais param e avaliar impacto das melhorias.

## Arquivos e componentes

`resources/views/site/partials/google-analytics.blade.php`, `resources/views/app/dashboard/index.blade.php`, controllers de cadastro e avaliação.

## Implementação

1. Definir eventos e propriedades mínimos: visita, cadastro, confirmação, primeiro cliente, primeira avaliação, PDF pronto, envio, checkout e assinatura.
2. Usar IDs anônimos/pseudônimos e não incluir nomes, medidas, fotos ou notas nos eventos; alinhar com 26.
3. Instrumentar backend para marcos que exigem confirmação real e frontend para interação; evitar contar clique como sucesso.
4. Criar funil semanal por coorte e origem, com definição de usuário ativo e conversão.
5. Revisar os números com logs/BD para detectar eventos ausentes ou duplicados.

## Critérios de aceite

- [ ] Funil distingue tentativa de sucesso em cada marco.
- [ ] Eventos não carregam dado clínico.
- [ ] Equipe consegue comparar coortes antes/depois de uma melhoria.

## Verificação

- Teste de emissão única dos eventos de backend.
- Conferência amostral entre painel e registros sintéticos.
