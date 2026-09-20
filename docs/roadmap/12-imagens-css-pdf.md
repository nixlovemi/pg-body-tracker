# Imagens e CSS de impressão

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** M
- **Dependências:** 06.

## Evidência e objetivo

`viewReportPDF.blade.php` carrega várias folhas gerais; fotos e logo viram Base64 na renderização.

Diminuir memória e tempo do DomPDF mantendo legibilidade de impressão.

## Arquivos e componentes

`resources/views/app/avaliation/viewReportPDF.blade.php`, `resources/views/components/avaliationReport/partials/pictures.blade.php`, `app/Traits/HasPhotoField.php`, CSS do relatório.

## Implementação

1. Medir tamanho e dimensões das fotos e do HTML/CSS embutido nos cenários 06.
2. Gerar derivativos de imagem proporcionais ao espaço impresso, com formato, qualidade e orientação definidos.
3. Criar CSS próprio de impressão apenas com regras usadas pelo PDF; revisar fontes e quebras de página.
4. Evitar imagens padrão repetidas quando não agregam valor; manter opção de fotos no relatório.
5. Comparar consumo máximo de memória, tamanho de arquivo e páginas com a linha de base.

## Critérios de aceite

- [ ] Fotos e logo aparecem com orientação e proporção corretas.
- [ ] PDF não corta tabelas ou cards nos casos de referência.
- [ ] Tempo, memória e tamanho são medidos e documentados.

## Verificação

- Renderizar amostras em papel A4 e inspecionar cada página.
- Testar sem fotos, com quatro fotos e com logo grande.
