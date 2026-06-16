# Origem Poços Artesianos

Site institucional da empresa **Origem Poços Artesianos**, desenvolvido como Projeto
Integrador do curso de Análise e Desenvolvimento de Sistemas (ADS / IMEPAC).

Stack: **WordPress 6.6 + PHP 8.2 + MySQL 8.0**, com camada **DevSecOps**, integração
com **Google Sheets** para captura de leads e ambientes Docker (dev) + shared hosting (prod).

- 🌐 **Produção:** [origin.adsimepac.com.br](https://origin.adsimepac.com.br)
- 🎨 **Design System:** Origem V2.0 (ver `CLAUDE.md`)
- 📋 **Política de segurança:** ver `SECURITY.md`

---

## Sumário

- [Visão geral](#visão-geral)
- [Arquitetura](#arquitetura)
- [Stack tecnológica](#stack-tecnológica)
- [Como rodar localmente](#como-rodar-localmente)
- [Estrutura do repositório](#estrutura-do-repositório)
- [Segurança e DevSecOps](#segurança-e-devsecops)
- [Integração com Google Sheets](#integração-com-google-sheets)
- [Deploy em produção](#deploy-em-produção)
- [Histórico das alterações](#histórico-das-alterações)
- [Próximos passos](#próximos-passos)

---

## Visão geral

O projeto entrega:

- **Site institucional** com 5 páginas: Início (home), Serviços, Sobre Nós, Portfólio e Contato
- **Formulário de orçamento** com proteção CSRF, honeypot anti-spam e rate-limit por IP
- **Captura automática de leads** em planilha do Google Drive (sem SaaS pago)
- **Pipeline DevSecOps** completa: SAST, scan de segredos, scan de IaC, hooks locais
- **17 vulnerabilidades** identificadas e mitigadas (cobertura completa do OWASP Top 10)
- **Painel de administração próprio** em *Configurações → Origem* para configurar a
  integração em qualquer ambiente (local, produção, gerenciado)

---

## Arquitetura

### Desenvolvimento (Docker Compose)

```
┌───────────────────────────────────────────────────────────┐
│  Máquina do desenvolvedor (localhost:8080)                │
│  ┌─────────────────────────────────────────────────────┐  │
│  │  Docker Compose stack                               │  │
│  │  ┌──────────────────┐    ┌───────────────────────┐ │  │
│  │  │  wordpress       │◀──▶│  db (MySQL 8.0)        │ │  │
│  │  │  apache + php82  │    │  rede: backend         │ │  │
│  │  │  bind 127.0.0.1  │    │  internal (oculta)     │ │  │
│  │  └──────────────────┘    └───────────────────────┘ │  │
│  └─────────────────────────────────────────────────────┘  │
└───────────────────────────────────────────────────────────┘
```

### Produção (hospedagem da faculdade)

```
Browser → origin.adsimepac.com.br (HTTPS · Nginx + Apache + PHP 8.1)
                                ↓ (form de contato)
                       WordPress 6.9.4 + Tema Origem
                                ↓ wp_remote_post
                       Google Apps Script (Web App)
                                ↓ valida token + grava
                       Google Sheets no Drive (auto-criada)
```

A planilha **é criada pelo próprio script** na primeira execução — o admin não precisa
configurar nada manualmente além de colar a URL no painel.

---

## Stack tecnológica

| Camada | Tecnologia | Versão |
|---|---|---|
| **Frontend** | HTML5 + CSS3 (Custom Properties / Grid / Flexbox) | — |
|  | JavaScript (vanilla, sem framework) | ES6+ |
| **Backend** | WordPress (CMS) | 6.6 (dev) / 6.9.4 (prod) |
|  | PHP | 8.2 (dev) / 8.1.34 (prod) |
|  | Tema customizado `origem-wp-theme` | 2.0 |
| **Banco** | MySQL | 8.0 |
| **Infra local** | Docker + Docker Compose | 29.2 / v5.0 |
| **Infra produção** | Nginx 1.14 + Apache | — |
| **Cloud** | Google Apps Script (Web App) | — |
|  | Google Sheets / Drive | — |
| **DevSecOps** | GitHub Actions (Gitleaks, PHPCS, Semgrep, Trivy) | — |
|  | pre-commit (hooks locais) | 4.6 |

---

## Como rodar localmente

### Pré-requisitos
- Docker Desktop (ou Docker Engine + Compose v2)
- Git
- (opcional) `pre-commit` para hooks locais

### Passo a passo

```bash
git clone https://github.com/alecioADS/OrigemPocos.git
cd OrigemPocos

# Copia o template e preenche com credenciais aleatórias
cp .env.example .env
# Edite .env e gere senhas fortes (sugestão: openssl rand -base64 32)
# Para as 8 keys/salts do WordPress, use: https://api.wordpress.org/secret-key/1.1/salt/

# Sobe o stack
docker compose up -d

# Aguarde o healthcheck do banco (~30s) e acesse:
open http://127.0.0.1:8080
```

Para ativar o tema "Origem Poços Artesianos" e criar as páginas, acesse o painel
admin (`/wp-admin/`) e use *Configurações → Origem → Executar configuração agora*.

### Comandos úteis

```bash
# Logs do container
docker compose logs -f wordpress

# Reset completo (apaga banco e uploads)
docker compose down -v

# Recarregar .env (restart simples NÃO recarrega env_file)
docker compose up -d --force-recreate wordpress

# Backup do banco
docker compose exec -T db mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" \
  --single-transaction origem_wp > backup-$(date +%Y%m%d).sql
```

### Hooks locais (opcional mas recomendado)

```bash
brew install pre-commit  # ou pip install pre-commit
pre-commit install
```

Cada commit passará por: detecção de chaves privadas, varredura de segredos com
Gitleaks, bloqueio de arquivos > 2MB, limpeza de whitespace, lint YAML.

---

## Estrutura do repositório

```
OrigemPocos/
├── docker-compose.yml          # Stack local (db + wordpress)
├── .env.example                # Template das variáveis sensíveis
├── .gitignore                  # Bloqueia .env, *.key, *.pem, logs
├── .gitleaks.toml              # Config do scanner de segredos
├── .pre-commit-config.yaml     # Hooks locais antes do commit
├── SECURITY.md                 # Política de divulgação responsável
├── CLAUDE.md                   # Design System V2.0 (paleta + tipografia)
├── README.md                   # Este arquivo
│
├── .github/
│   └── workflows/
│       └── security.yml        # Pipeline DevSecOps (CI/CD)
│
├── docs/
│   ├── google-sheets-setup.md       # Tutorial passo a passo
│   └── google-sheets-apps-script.js # Código pronto para colar
│
└── origem-wp-theme/            # Tema customizado WordPress
    ├── style.css                    # Design System + estilos
    ├── functions.php                # Hooks, segurança, settings page
    ├── header.php · footer.php      # Layout comum
    ├── front-page.php               # Home (hero + stats + serviços + diff)
    ├── page-servicos.php
    ├── page-sobre.php
    ├── page-portfolio.php
    ├── page-contato.php             # Formulário de orçamento
    ├── index.php                    # Fallback
    └── assets/
        ├── images/                  # Logo, hero, WhatsApp, portfolio
        └── js/main.js               # Interações UI (sem framework)
```

---

## Segurança e DevSecOps

### 17 vulnerabilidades mitigadas

Resumo (detalhamento completo em `SECURITY.md`):

| Categoria | Mitigação aplicada |
|---|---|
| **Credenciais hardcoded** | Movidas para `.env` (`.gitignore`), regeneradas com `openssl rand` |
| **Hardening Docker** | Rede `internal`, bind `127.0.0.1`, `no-new-privileges`, tema read-only |
| **CSRF do formulário** | `wp_nonce_field` + `wp_verify_nonce` + honeypot anti-bot |
| **Rate-limit** | WP Transient por IP (30 s entre envios bem-sucedidos) |
| **E-mail header injection** | Helper `origem_strip_crlf()` remove `\r\n` antes do `wp_mail()` |
| **Cabeçalhos HTTP** | CSP, X-Frame-Options, nosniff, Referrer-Policy, Permissions-Policy, COOP |
| **XML-RPC** | Filter `xmlrpc_enabled` → false; X-Pingback removido |
| **Enumeração de usuários** | `?author=N` redireciona; REST `/wp/v2/users` bloqueado para anônimos |
| **Fingerprinting** | `wp_generator` removido; `ver=` filtrado em scripts/estilos |
| **Erro de login** | Mensagem genérica "Credenciais inválidas" |
| **Edição de arquivos** | `DISALLOW_FILE_EDIT = true` |
| **Sanitização** | `wp_unslash` + `sanitize_*` em todos os inputs; `esc_*` em todas as saídas |
| **Limites de tamanho** | `mb_substr()` em todos os campos do formulário |

### Pipeline CI/CD (`.github/workflows/security.yml`)

Executa em cada PR/push e semanalmente:

- **Gitleaks** — varredura de segredos no histórico
- **PHPCS** + WordPress Coding Standards / WordPress-VIP-Go
- **Semgrep** — regras `p/php`, `p/security-audit`, `p/owasp-top-ten`, `p/secrets`
- **Trivy** — scan de filesystem, IaC (Docker Compose) e da imagem WordPress

### Cobertura OWASP Top 10 (2021)

| Categoria | Status |
|---|---|
| A01 Broken Access Control | ✅ |
| A02 Cryptographic Failures | ✅ |
| A03 Injection | ✅ |
| A04 Insecure Design | ✅ |
| A05 Security Misconfiguration | ✅ |
| A06 Vulnerable Components | ✅ |
| A07 Authentication Failures | ✅ |
| A08 Software & Data Integrity | ✅ |
| A09 Logging & Monitoring | ⚠ Parcial (recomendado SIEM em prod) |
| A10 SSRF | ✅ |

---

## Integração com Google Sheets

Cada envio do formulário `/contato/` cai automaticamente em uma planilha do Drive.
O design segue o padrão **standalone webhook** com token compartilhado.

### Como funciona

1. O cliente preenche o formulário em `/contato/`
2. O WordPress valida (nonce, honeypot, sanitização, rate-limit)
3. Envia via `wp_remote_post` para o Google Apps Script (server-to-server)
4. O Apps Script valida o token e grava a linha na planilha
5. Se for o primeiro envio, **a planilha é criada automaticamente** no Drive da
   conta que implantou o script

### Configuração em três camadas (fallback)

O tema lê as credenciais nessa ordem:

1. **Constante PHP** em `wp-config.php`:
   ```php
   define('ORIGEM_SHEETS_WEBHOOK_URL', 'https://script.google.com/.../exec');
   define('ORIGEM_SHEETS_TOKEN', '...');
   ```
2. **Option do WordPress** salva em *Configurações → Origem* (usado em produção)
3. **Variável de ambiente** (vinda do `.env` via Docker, usado em desenvolvimento)

### Setup (5 minutos)

Ver `docs/google-sheets-setup.md` para o passo a passo completo.
O Apps Script está em `docs/google-sheets-apps-script.js`.

### Segurança da integração

- O token **nunca** vai ao navegador do visitante — só sai do servidor PHP
- Sem token correto, o script retorna `unauthorized` e não grava nada
- Tráfego sempre HTTPS (Google obriga)
- Se o webhook falhar, o `wp_mail` é canal de fallback independente

---

## Deploy em produção

### Pré-requisitos
- Hospedagem com WordPress já instalado (5.6+ idealmente)
- Acesso wp-admin como Administrator
- Domínio com HTTPS

### Passos

1. Empacotar o tema:
   ```bash
   zip -r origem-wp-theme.zip origem-wp-theme \
     -x "*.DS_Store" "*_originals/*"
   ```

2. **Appearance → Themes → Add New → Upload Theme** no wp-admin, fazer upload
   do zip e clicar em **Ativar**.

3. Acessar **Configurações → Origem**, clicar em
   **Executar configuração agora** (cria as 5 páginas com templates, define a
   home estática e ajusta os permalinks para `/%postname%/`).

4. Salvar a URL e o token do Apps Script no mesmo painel e clicar em
   **Enviar linha de teste para a planilha** para validar.

---

## Histórico das alterações

Resumo do que foi feito em comparação com o repositório base:

### Camada de segurança (DevSecOps)
- Removidas senhas hardcoded do `docker-compose.yml`
- Adicionado `.env.example` + reforço no `.gitignore`
- Adicionado `SECURITY.md` e `.gitleaks.toml`
- Pipeline `.github/workflows/security.yml` com 6 ferramentas
- Hooks locais em `.pre-commit-config.yaml`
- 17 vulnerabilidades de código corrigidas no tema

### Redesign visual da home
- Nova logo (`logoorigemfinal.png`) com transparência, exibida em 140px
- Hero refeito com foto de fundo (full-bleed + gradient overlay diagonal)
- Stats strip flutuante (4 cards brancos sobre o hero)
- Seção de serviços redesenhada (cards com ícones gradientes)
- Nova seção de **Diferenciais** (4 cards com check marks)
- Novo CTA banner gradiente navy → azul-água
- WhatsApp flutuante usando `wap.png` com animação de pulse
- Imagens otimizadas: **10.8 MB → 2.6 MB**

### Funcionalidades novas
- Integração com Google Sheets via Apps Script standalone
- Honeypot anti-spam no formulário
- Rate-limit por IP via Transients
- Painel próprio em *Configurações → Origem*:
  - URL e token do webhook
  - Teste de conexão
  - Botão de "Configurar site" (cria páginas + permalinks + home)
- Headers de segurança HTTP (CSP, X-Frame-Options, etc.)
- Bloqueio de XML-RPC e enumeração de usuários

### Docker hardening
- `.env` para todas as credenciais (com escape `$$` para salts)
- Rede `backend: internal` (banco não exposto)
- `127.0.0.1:8080` (não exposto à rede)
- `security_opt: no-new-privileges:true`
- Healthcheck do MySQL
- Tema montado como `:ro`
- `WORDPRESS_CONFIG_EXTRA` com `DISALLOW_FILE_EDIT`, `WP_AUTO_UPDATE_CORE`, salts

---

## Próximos passos

- [ ] Migrar para `origem.adsimepac.com.br` (com "e") quando a TI da faculdade criar
- [ ] Configurar SMTP autenticado (Brevo / SES — tier free) para `wp_mail()`
- [ ] Habilitar 2FA para o usuário admin (plugin Two-Factor oficial)
- [ ] Adicionar meta tags Open Graph para previews em redes sociais
- [ ] Backups automáticos do banco + uploads
- [ ] Monitoramento de uptime (UptimeRobot ou healthcheck.io)
- [ ] Centralização de logs (CloudWatch / Loki / Datadog)

---

## Licença

Este projeto tem **duas camadas distintas** de propriedade — o código é livre, mas a
identidade visual da empresa é protegida.

### Código-fonte

O código PHP, CSS, JavaScript e configurações deste repositório
(`origem-wp-theme/`, `docker-compose.yml`, `.github/`, `docs/`, etc.) está sob a
licença **GNU General Public License v2 ou posterior (GPLv2+)**, herdada do
WordPress.

Isso significa que você **pode**:

- ✅ Usar o código em projetos pessoais, acadêmicos ou comerciais
- ✅ Modificar, adaptar e redistribuir
- ✅ Estudar a implementação para fins de aprendizado
- ✅ Incorporar trechos em outros projetos (respeitando GPLv2)

Você **deve**:

- 📋 Manter o aviso de copyright e a referência à licença
- 📋 Distribuir versões modificadas também sob GPLv2+
- 📋 Disponibilizar o código-fonte se redistribuir um produto derivado

### Identidade visual e conteúdo da empresa

Os seguintes itens **NÃO** estão sob GPL — são propriedade da **Origem Poços
Artesianos** e não podem ser reutilizados sem autorização expressa:

- Marca, logo e nome "Origem Poços Artesianos"
   (`assets/images/logoorigemfinal.png`, `assets/images/logo.jpeg`)
- Fotografias da equipe e das obras
   (`hero-home.png`, `img-sobre.png`, `port1.jpeg` ... `port6.jpeg`)
- Textos institucionais sobre serviços, histórico e portfólio
- Dados de contato (telefone, endereço, área de atuação)

Se quiser usar este tema como base para outro projeto, **substitua todos esses
itens** pelos da sua empresa antes de publicar.

### Bibliotecas de terceiros

Mantêm suas licenças originais:

- **WordPress** — GPLv2+ (<https://wordpress.org/about/license/>)
- **Inter font** (Google Fonts) — SIL Open Font License 1.1
- **Ícone do WhatsApp** — marca registrada da Meta Platforms, usado conforme
  diretrizes para apps de terceiros (<https://about.meta.com/brand/resources/whatsapp/whatsapp-brand/>)

### Contexto acadêmico

Este projeto foi desenvolvido como **Projeto Integrador** do curso de Análise e
Desenvolvimento de Sistemas (ADS) do **IMEPAC**. O uso é livre para fins
educacionais, com crédito ao autor original quando apropriado.

### Contato

Dúvidas sobre licenciamento ou uso comercial: abra uma issue no repositório
ou entre em contato com a Origem Poços Artesianos.
