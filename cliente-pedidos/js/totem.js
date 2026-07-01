/* =============================================================
 * totem.js — Identificação do totem (quiosque)
 * -------------------------------------------------------------
 * Guarda qual totem está operando este dispositivo. O id e o nome
 * ficam no localStorage e acompanham cada pedido enviado ao backend.
 *
 * NÃO faz redirecionamento automático ao carregar: cada página
 * decide, no seu próprio DOMContentLoaded, se exige o setup
 * (via Totem.isSetup()). Isso evita loops de redirect.
 * ============================================================= */

(function () {
  "use strict";

  const KEY_ID = "totem_id";
  const KEY_NOME = "totem_nome";

  window.Totem = {
    // Número do totem (string) ou null se não configurado.
    getId() {
      return localStorage.getItem(KEY_ID);
    },

    // Nome opcional do totem ("" quando não informado).
    getNome() {
      return localStorage.getItem(KEY_NOME) || "";
    },

    // true se este dispositivo já tem um totem configurado.
    isSetup() {
      return !!localStorage.getItem(KEY_ID);
    },

    // Salva a identificação e volta para a tela inicial.
    setup(id, nome) {
      localStorage.setItem(KEY_ID, id);
      localStorage.setItem(KEY_NOME, nome || "");
      window.location.href = "index.html";
    },

    // Remove a identificação do totem deste dispositivo.
    clear() {
      localStorage.removeItem(KEY_ID);
      localStorage.removeItem(KEY_NOME);
    },

    // Rótulo amigável, ex.: "Totem 5 — Caixa 2".
    label() {
      const id = this.getId();
      const nome = this.getNome();
      return "Totem " + id + (nome ? " — " + nome : "");
    },
  };
})();
