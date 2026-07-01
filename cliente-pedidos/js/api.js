/* =============================================================
 * api.js — Camada centralizada de acesso à API
 * -------------------------------------------------------------
 * TODA a comunicação fetch com o backend fica aqui. As telas NÃO
 * chamam fetch diretamente; elas usam as funções de window.API.
 *
 * Para apontar para outro backend, altere APENAS a constante
 * API_BASE abaixo. Quando o backend CI4 real estiver disponível,
 * basta trocar essa única linha.
 * ============================================================= */

(function () {
  "use strict";

  // -----------------------------------------------------------
  // Configuração — ÚNICA constante a mudar entre ambientes
  // -----------------------------------------------------------
  // Escolha o ambiente alterando APENAS esta constante:
  //   Backend real (Windows/Laragon): http://localhost/ProgWebIII/backend-pedidos/public/api
  //   Backend real (Mac/Linux, php spark serve): http://localhost:8080/api
  //   Mock server (node mock-server.js):          http://localhost:3001/api
  const API_BASE = "http://localhost/ProgWebIII/backend-pedidos/public/api";

  // Chave de API exigida pelo endpoint de checkout.
  const API_KEY = "D41D8CD98F00B204E9800998ECF8427E";

  // -----------------------------------------------------------
  // Helper interno de requisição
  // -----------------------------------------------------------
  async function request(path, options = {}) {
    const url = `${API_BASE}${path}`;
    const config = {
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        // TODO Parte 2: adicionar token de autenticação nos headers,
        // por exemplo: Authorization: `Bearer ${Auth.getToken()}`
        ...(options.headers || {}),
      },
      ...options,
    };

    let response;
    try {
      response = await fetch(url, config);
    } catch (networkError) {
      // Falha de rede / CORS / servidor offline — mensagem amigável.
      throw new Error(
        "Não foi possível conectar à API. Verifique se o servidor está " +
          "rodando (node mock-server.js)."
      );
    }

    // Tenta interpretar o corpo como JSON; senão, mantém como texto.
    let payload = null;
    const text = await response.text();
    if (text) {
      try {
        payload = JSON.parse(text);
      } catch (_) {
        payload = text;
      }
    }

    if (!response.ok) {
      const msg =
        (payload && (payload.message || payload.error)) ||
        `Erro ${response.status} ao acessar ${path}`;
      throw new Error(msg);
    }

    return payload;
  }

  // -----------------------------------------------------------
  // Endpoints
  // -----------------------------------------------------------

  /**
   * GET /status — verifica se a API está no ar.
   * @returns {Promise<Object>} { status, mensagem }
   */
  async function getStatus() {
    return request("/status", { method: "GET" });
  }

  /**
   * GET /produtos — lista de produtos disponíveis.
   * Aceita um array direto ou { data: [...] } / { produtos: [...] }.
   * @returns {Promise<Array>}
   */
  async function getProdutos() {
    const data = await request("/produtos", { method: "GET" });
    if (Array.isArray(data)) return data;
    if (data && Array.isArray(data.data)) return data.data;
    if (data && Array.isArray(data.produtos)) return data.produtos;
    return [];
  }

  /**
   * POST /checkout — registra o pedido (envia o header apiKey).
   * @param {Object} pedido  { status: "novo", produtos: [...] }
   * @returns {Promise<Object>} { status, message, id_pedido }
   */
  async function postCheckout(pedido) {
    return request("/checkout", {
      method: "POST",
      headers: {
        apiKey: API_KEY,
      },
      body: JSON.stringify(pedido),
    });
  }

  // -----------------------------------------------------------
  // Exposição global
  // -----------------------------------------------------------
  window.API = {
    API_BASE,
    getStatus,
    getProdutos,
    postCheckout,
  };
})();
