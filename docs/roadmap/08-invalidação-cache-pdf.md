# Invalidação correta do PDF

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 06.

## Evidência e objetivo

`buildSnapshotHash()` inclui atributos da avaliação atual e alguns timestamps; o PDF usa histórico e dados do perfil. `UserInfo` não mantém `updated_at`, e fotos podem ser substituídas no mesmo caminho.

Servir arquivo em cache somente quando todos os dados e a versão do template correspondem ao conteúdo.

## Arquivos e componentes

`app/Services/AvaliationPdfCacheService.php`, `app/Models/Avaliation.php`, `app/Models/UserInfo.php`, `app/Traits/HasPhotoField.php`.

## Implementação

1. Definir dependências de cada variante: avaliação atual, avaliações históricas usadas, cliente, perfil, logo, fotos, idioma e versão do layout.
2. Gerar hash estável com versões ou `updated_at` confiáveis; avaliar digest dos dados selecionados quando não houver versionamento.
3. Incluir opções de gráficos/fotos e versão de template no identificador; manter cache antigo apenas até expiração segura.
4. Gravar arquivo temporário e publicar estado `ready` somente após escrita completa; preservar versões acessadas por links ainda válidos.
5. Prever limpeza de arquivos órfãos e de entradas pendentes antigas.

## Critérios de aceite

- [ ] Editar avaliação histórica, perfil ou foto produz PDF atualizado.
- [ ] Reabrir sem alterações retorna o arquivo existente.
- [ ] Nenhum PDF parcial é servido.

## Verificação

- Teste de invalidação para histórico, perfil, foto e variante.
- Teste de concorrência de duas solicitações do mesmo snapshot.
