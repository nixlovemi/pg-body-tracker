# Atualização da plataforma

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** 20 e 21.

## Evidência e objetivo

`composer.json` declara Laravel 8; `Dockerfile` usa PHP 8.0.28; `docker-compose.yml` usa MySQL 5.7. São bases antigas para evolução e correções de segurança.

Chegar a versões suportadas com migração reversível e cobertura dos fluxos de faturamento, autenticação e PDF.

## Arquivos e componentes

`composer.json`, `composer.lock`, `Dockerfile`, `docker-compose.yml`, `package.json`, `.github/workflows/ci-tests.yml`.

## Implementação

1. Levantar versões efetivas de produção, extensões PHP, banco e dependências que limitam o upgrade; registrar matriz de compatibilidade.
2. Criar imagem de desenvolvimento/homologação fixa e reproduzível, evitando `composer:latest` no build.
3. Atualizar PHP e Laravel em saltos compatíveis, resolvendo pacotes abandonados e mudanças de API em commits separados.
4. Ensaiar migrações, tarefas, geração PDF, pagamentos e login Google em cópia anonimizada ou dados sintéticos.
5. Definir janela, backup verificado, monitoramento pós-deploy e estratégia de reversão de aplicação e esquema.

## Critérios de aceite

- [ ] Framework e PHP de produção estão em versões com suporte.
- [ ] Suíte crítica e cenários manuais passam na nova imagem.
- [ ] Plano de retorno foi ensaiado antes da migração.

## Verificação

- Executar CI e matriz de smoke tests em homologação.
- Comparar geração de PDF e consultas antes/depois.
