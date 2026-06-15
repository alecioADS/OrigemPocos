/**
 * Origem Poços Artesianos — Webhook do formulário de contato.
 *
 * Como usar:
 *   1. Acesse https://script.google.com
 *   2. "Novo projeto" — apague o código padrão e cole TODO este arquivo
 *   3. Em EXPECTED_TOKEN cole o mesmo valor de ORIGEM_SHEETS_TOKEN do .env
 *   4. Salve (Ctrl+S) com um nome, ex.: "Webhook Origem"
 *   5. Botão "Implantar" → "Nova implantação"
 *        - Tipo: App da Web
 *        - Executar como: Eu
 *        - Quem tem acesso: Qualquer pessoa
 *      Autorize quando solicitado e copie a URL (termina em /exec)
 *   6. Cole a URL em ORIGEM_SHEETS_WEBHOOK_URL no .env
 *
 * A planilha é CRIADA AUTOMATICAMENTE no Drive da conta que implantou
 * o script, no primeiro envio bem-sucedido. Você receberá um e-mail
 * com o link da planilha. As próximas linhas entram na MESMA planilha.
 */

const EXPECTED_TOKEN     = 'COLE_AQUI_O_MESMO_TOKEN_DO_ENV';
const SPREADSHEET_NAME   = 'Origem Poços Artesianos — Contatos';
const SHEET_TAB_NAME     = 'Contatos';
const NOTIFY_OWNER_EMAIL = true;   // envia um e-mail quando a planilha for criada

const HEADERS = [
  'Data/Hora', 'Nome', 'Telefone', 'Endereço',
  'Tipo de propriedade', 'Profundidade', 'Observações', 'IP de origem'
];

function doPost(e) {
  try {
    if (!e || !e.postData || !e.postData.contents) {
      return _json({ ok: false, error: 'missing_payload' });
    }

    const data = JSON.parse(e.postData.contents);

    // Autenticação via token compartilhado.
    if (!data.token || data.token !== EXPECTED_TOKEN) {
      return _json({ ok: false, error: 'unauthorized' });
    }

    const sheet = _getOrCreateSpreadsheet();

    sheet.appendRow([
      data.data_iso  || new Date().toISOString(),
      data.nome      || '',
      data.telefone  || '',
      data.endereco  || '',
      data.tipo      || '',
      data.prof      || '',
      data.obs       || '',
      data.origem_ip || ''
    ]);

    return _json({ ok: true });

  } catch (err) {
    return _json({ ok: false, error: String(err) });
  }
}

function doGet() {
  // Endpoint de saúde — útil para testar a URL no navegador.
  return _json({ ok: true, service: 'Origem contact webhook' });
}

/**
 * Retorna a aba da planilha vinculada. Se ainda não existe, cria a
 * planilha no Drive da conta e guarda o ID nas propriedades do script.
 */
function _getOrCreateSpreadsheet() {
  const props = PropertiesService.getScriptProperties();
  let id = props.getProperty('SPREADSHEET_ID');

  let ss;
  if (id) {
    try {
      ss = SpreadsheetApp.openById(id);
    } catch (e) {
      // Planilha deletada/lixeira — recria.
      id = null;
    }
  }

  if (!id) {
    ss = SpreadsheetApp.create(SPREADSHEET_NAME);
    props.setProperty('SPREADSHEET_ID', ss.getId());

    const sheet = ss.getActiveSheet();
    sheet.setName(SHEET_TAB_NAME);
    sheet.appendRow(HEADERS);
    sheet.getRange(1, 1, 1, HEADERS.length)
         .setFontWeight('bold')
         .setBackground('#0A2540')
         .setFontColor('#FFFFFF');
    sheet.setFrozenRows(1);
    sheet.autoResizeColumns(1, HEADERS.length);

    if (NOTIFY_OWNER_EMAIL) {
      try {
        MailApp.sendEmail({
          to: Session.getActiveUser().getEmail(),
          subject: 'Planilha de contatos criada — Origem Poços',
          htmlBody:
            '<p>A planilha de leads do site foi criada com sucesso e está em seu Google Drive.</p>' +
            '<p><strong>Link:</strong> <a href="' + ss.getUrl() + '">' + ss.getUrl() + '</a></p>' +
            '<p>Os próximos formulários preenchidos serão adicionados automaticamente nesta planilha.</p>'
        });
      } catch (e) { /* sem permissão MailApp — ignora */ }
    }
  }

  let tab = ss.getSheetByName(SHEET_TAB_NAME);
  if (!tab) {
    tab = ss.insertSheet(SHEET_TAB_NAME);
    tab.appendRow(HEADERS);
    tab.setFrozenRows(1);
  }
  return tab;
}

function _json(obj) {
  return ContentService
    .createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}
