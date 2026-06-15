# Integração — Formulário de Contato → Google Sheets

A cada envio do formulário de `/contato/`, os dados caem **automaticamente**
em uma planilha do Google Drive. A planilha é **criada pelo próprio script**
na primeira execução — você não precisa criar nada à mão.

## Como funciona

```
Cliente preenche o form
        │
        ▼
WordPress (nonce + honeypot + rate-limit + sanitização)
        │
        ├─ wp_mail → admin (canal 1)
        │
        └─ POST com token → Apps Script → Planilha no Drive (canal 2)
```

A autenticação entre WordPress e Apps Script é feita por um **token compartilhado**
(`ORIGEM_SHEETS_TOKEN`). Sem token válido, o script retorna `unauthorized`
e não grava nada.

## Passo a passo (5 minutos)

### 1. Acesse o Apps Script
- Abra <https://script.google.com> com a **conta Google que será dona da planilha**
  (sugestão: conta corporativa da Origem, não a sua pessoal)
- Botão **+ Novo projeto**

### 2. Cole o código
- Apague o `function myFunction() {}` padrão
- Cole o conteúdo de `docs/google-sheets-apps-script.js`
- Localize a linha:
  ```js
  const EXPECTED_TOKEN = 'COLE_AQUI_O_MESMO_TOKEN_DO_ENV';
  ```
  e troque pelo valor de `ORIGEM_SHEETS_TOKEN` do arquivo `.env`
- Salve com Ctrl+S — dê um nome ao projeto, ex.: `Webhook Origem`

### 3. Implante como Web App
- Botão **Implantar → Nova implantação**
- Engrenagem ⚙ ao lado de "Selecionar tipo" → **App da Web**
- Configurações:
  - **Descrição:** `Webhook contato Origem`
  - **Executar como:** Eu (sua conta)
  - **Quem tem acesso:** Qualquer pessoa
- Clique **Implantar**
- Autorize quando o Google pedir (clique em "Avançado" → "Acessar projeto" se aparecer aviso de "App não verificado" — é normal pois é seu próprio script)
- Copie a URL final que termina em `/exec`

### 4. Configure no WordPress
No arquivo `.env`:
```env
ORIGEM_SHEETS_WEBHOOK_URL=https://script.google.com/macros/s/AKfy.../exec
ORIGEM_SHEETS_TOKEN=<o-token-que-você-já-tem-no-.env>
```
Reinicie o WordPress:
```bash
docker compose restart wordpress
```

### 5. Pronto — teste
Acesse `http://127.0.0.1:8080/contato/` (ou seu domínio em produção),
preencha o formulário e clique enviar.

Na primeira vez:
- O script **cria a planilha** chamada `Origem Poços Artesianos — Contatos`
  no Google Drive da conta que implantou
- Você recebe um **e-mail com o link** dessa planilha
- A linha do formulário já entra registrada

A partir daí, todo formulário cai nessa mesma planilha automaticamente.

## Estrutura da planilha

Cabeçalho navy com texto branco, primeira linha congelada:

| Data/Hora | Nome | Telefone | Endereço | Tipo de propriedade | Profundidade | Observações | IP de origem |
|---|---|---|---|---|---|---|---|

## Localizar a planilha

Se perder o e-mail:
- <https://drive.google.com> → busque por `Origem Poços Artesianos — Contatos`
- Ou volte ao Apps Script → menu **Editor → Projeto** → **Propriedades do script**
  → o `SPREADSHEET_ID` está lá. Acesse `https://docs.google.com/spreadsheets/d/<ID>/edit`

## Trocar a conta dona

Se mais tarde quiser migrar para outra conta Google:

1. Crie nova implantação do script na **nova conta**
2. Atualize `ORIGEM_SHEETS_WEBHOOK_URL` no `.env` com a nova URL
3. `docker compose restart wordpress`
4. A planilha antiga continua na conta velha (com o histórico) — você pode
   exportar/importar manualmente se quiser unificar.

## Trocar o token

1. Gere novo token: `openssl rand -hex 24`
2. Atualize `.env` no campo `ORIGEM_SHEETS_TOKEN`
3. No Apps Script, troque `EXPECTED_TOKEN` pelo mesmo valor → **Salvar**
   (sem precisar reimplantar — mantém a mesma URL)
4. `docker compose restart wordpress`

## Segurança

- O token vai **server-to-server** (PHP → Google), nunca chega ao navegador do visitante.
- Sem token correto, o script responde `unauthorized` — nada é gravado.
- Tráfego sempre HTTPS (Google Apps Script obriga).
- Se a planilha falhar (rede/quota/etc.), o `wp_mail` é independente — o
  formulário ainda chega no e-mail do admin.
