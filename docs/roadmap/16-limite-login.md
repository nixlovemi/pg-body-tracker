# Proteção de login e recuperação

- **Prioridade:** P1
- **Estado:** planejado
- **Esforço relativo:** P
- **Dependências:** Nenhuma.

## Evidência e objetivo

Rotas `doLogin`, `doForgot` e cadastro em `routes/web.php` não aplicam middleware de taxa; a limitação existente cobre a API.

Reduzir tentativa automatizada de senha e abuso de envio de e-mails sem bloquear o uso legítimo.

## Arquivos e componentes

`routes/web.php`, `app/Http/Controllers/Login.php`, `app/Providers/RouteServiceProvider.php`, `config/cache.php`.

## Implementação

1. Definir limites por IP e identificador normalizado, com janelas distintas para login e recuperação.
2. Aplicar rate limiter nas rotas e resposta genérica que não confirma existência da conta.
3. Registrar contagem agregada de bloqueios e falhas sem armazenar senha.
4. Revisar fluxo Google e confirmação para limites de requisição e reenvio.
5. Documentar forma de liberar bloqueio indevido e verificar efeito do proxy sobre IP de origem.

## Critérios de aceite

- [ ] Tentativas excessivas recebem limite consistente.
- [ ] Conta inexistente e existente não se diferenciam pelo texto de recuperação.
- [ ] Usuário legítimo consegue tentar novamente após janela definida.

## Verificação

- Testes de limite, reset da janela e endereço IP.
- Teste do fluxo normal de login e recuperação.
