# Política de Segurança — Origem Poços Artesianos

## Versões suportadas

| Versão do tema | Suporte de segurança |
|---|---|
| 2.x | Sim |
| < 2.0 | Não |

## Como reportar uma vulnerabilidade

Envie um e-mail para **security@origempocos.com.br** descrevendo:

- Resumo do problema
- Passos para reproduzir
- Impacto estimado
- Versão afetada (commit SHA, se aplicável)

Não abra issues públicas para vulnerabilidades. Resposta em até 72 horas úteis.

## Práticas DevSecOps adotadas

### Código
- Toda saída HTML usa `esc_html`/`esc_attr`/`esc_url`
- Inputs de `$_POST`/`$_GET`/`$_SERVER` passam por `wp_unslash` + função de sanitização apropriada
- Formulários protegidos com `wp_nonce_field`/`wp_verify_nonce` (CSRF)
- Anti-spam: honeypot + rate limit por IP (transient)
- Defesa contra e-mail header injection (CRLF strip)
- Limites de tamanho em todos os campos do formulário

### Configuração
- Sem segredos no código — `.env` lido pelo Docker Compose
- `DISALLOW_FILE_EDIT` ativo (sem edição de arquivos pelo painel)
- XML-RPC desabilitado
- Enumeração de usuários bloqueada (`?author=N` e REST `/wp/v2/users`)
- Mensagens genéricas em erros de login
- Geradores/versões removidos do front (anti-fingerprinting)

### HTTP
- Headers configurados em `functions.php` (send_headers):
  - `Content-Security-Policy`
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy`
  - `Cross-Origin-Opener-Policy: same-origin`

### Infraestrutura
- MySQL na rede `internal` (não exposto à rede externa)
- WordPress publicado apenas em `127.0.0.1:8080` (uso atrás de reverse proxy)
- `security_opt: no-new-privileges` em todos os serviços
- Healthcheck no banco
- Tema montado como `:ro` no contêiner

### Pipeline (GitHub Actions)
- **gitleaks** — varredura de segredos
- **PHPCS + WordPress-VIP / Coding Standards** — análise estática
- **Trivy** — scan de imagens Docker e do filesystem
- **Semgrep** — regras de segurança para PHP

## Checklist pré-deploy

- [ ] `.env` em produção tem senhas fortes (mínimo 24 caracteres, geradas aleatoriamente)
- [ ] Salts/keys do WordPress gerados em <https://api.wordpress.org/secret-key/1.1/salt/>
- [ ] Reverse proxy (Nginx/Caddy) com TLS válido (Let's Encrypt) na frente do contêiner
- [ ] Backups automáticos do volume `db_data`
- [ ] WAF (Cloudflare/ModSecurity) habilitado
- [ ] Logs centralizados e monitorados
