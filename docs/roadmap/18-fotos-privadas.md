# Fotos privadas e uploads validados

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** G
- **Dependências:** 01.

## Evidência e objetivo

`Avaliation::setPhotoUrl()` gera nome previsível; `Avaliation::showPhoto()` recebe nome de arquivo e verifica só existência. Imagens também são incorporadas a relatórios.

Garantir que imagens de pacientes só saiam por autorização explícita e tenham ciclo de vida controlado.

## Arquivos e componentes

`app/Models/Avaliation.php`, `app/Traits/HasPhotoField.php`, `app/Http/Controllers/Avaliation.php`, `config/filesystems.php`.

## Implementação

1. Mapear pasta de armazenamento, URL pública, rota de foto e usos de Base64; confirmar se há acesso direto pelo servidor web.
2. Associar pedido de foto a avaliação e lado da imagem, resolver arquivo a partir do banco e validar proprietário ou link assinado.
3. Validar MIME real, extensão, tamanho e dimensões; normalizar orientação e gerar derivativos sem metadados dispensáveis.
4. Usar nomes opacos e guardar em disco privado; migrar arquivos existentes com compatibilidade temporária.
5. Definir exclusão de imagem antiga, PDF em cache e derivativos quando a foto é substituída ou o cliente excluído.

## Critérios de aceite

- [ ] ID ou nome adivinhado de outra conta não entrega foto.
- [ ] Arquivo inválido ou excessivo é rejeitado com mensagem clara.
- [ ] Substituição e exclusão removem derivados sem quebrar relatórios autorizados.

## Verificação

- Testes de autorização com duas contas e link assinado.
- Testes de upload válido, formato falso, arquivo grande e orientação.
