/* =============================================================
 * mock-server.js — Servidor de teste em Node.js puro
 * -------------------------------------------------------------
 * Simula o backend CodeIgniter 4 enquanto ele não está disponível.
 * Sem dependências externas (sem express). Rode com:
 *
 *     node mock-server.js
 *
 * Endpoints (CORS liberado para qualquer origem):
 *   GET  /api/status    → { status: "ok", mensagem: "Api funcionando" }
 *   GET  /api/produtos  → array com 6 produtos (3 Lanches, 3 Bebidas)
 *   POST /api/checkout  → valida o header apiKey e devolve { status, id_pedido }
 *
 * O API_BASE de js/api.js deve apontar para http://localhost:3001/api.
 * ============================================================= */

"use strict";

const http = require("http");

// -------------------------------------------------------------
// Configuração
// -------------------------------------------------------------
const PORT = 3001;

// Mesma chave exigida pelo backend real / esperada em js/api.js.
const API_KEY = "D41D8CD98F00B204E9800998ECF8427E";

// Contador de pedidos — incrementa a cada checkout bem-sucedido.
let proximoIdPedido = 1;

// Catálogo fixo de produtos (mesmo formato do backend CI4 real).
const PRODUTOS = [
  {
    id: 1,
    nome: "X-Burguer",
    preco: 18.9,
    tipo: "Lanches",
    descricao: "Pão, hambúrguer, queijo e salada.",
    disponivel: 1,
  },
  {
    id: 2,
    nome: "X-Salada",
    preco: 21.5,
    tipo: "Lanches",
    descricao: "Hambúrguer, queijo, alface, tomate e maionese.",
    disponivel: 1,
  },
  {
    id: 3,
    nome: "X-Bacon",
    preco: 24.0,
    tipo: "Lanches",
    descricao: "Hambúrguer, bacon crocante, queijo e cebola.",
    disponivel: 1,
  },
  {
    id: 4,
    nome: "Coca-Cola",
    preco: 7.0,
    tipo: "Bebidas",
    descricao: "Lata 350ml gelada.",
    disponivel: 1,
  },
  {
    id: 5,
    nome: "Suco de Laranja",
    preco: 9.5,
    tipo: "Bebidas",
    descricao: "Natural, copo 500ml.",
    disponivel: 1,
  },
  {
    id: 6,
    nome: "Água Mineral",
    preco: 4.5,
    tipo: "Bebidas",
    descricao: "Sem gás, garrafa 500ml.",
    disponivel: 1,
  },
];

// -------------------------------------------------------------
// Helpers
// -------------------------------------------------------------

// Aplica os cabeçalhos de CORS em toda resposta.
function aplicarCors(res) {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
  // apiKey precisa ser permitido aqui para o preflight do POST passar.
  res.setHeader(
    "Access-Control-Allow-Headers",
    "Content-Type, Accept, apiKey"
  );
}

// Envia uma resposta JSON com o status informado.
function enviarJson(res, status, dados) {
  aplicarCors(res);
  res.writeHead(status, { "Content-Type": "application/json; charset=utf-8" });
  res.end(JSON.stringify(dados));
}

// Lê o corpo (body) de uma requisição e devolve o JSON parseado.
function lerBody(req) {
  return new Promise((resolve) => {
    let raw = "";
    req.on("data", (chunk) => (raw += chunk));
    req.on("end", () => {
      if (!raw) return resolve({});
      try {
        resolve(JSON.parse(raw));
      } catch (_) {
        resolve({});
      }
    });
  });
}

// -------------------------------------------------------------
// Servidor
// -------------------------------------------------------------
const server = http.createServer(async (req, res) => {
  const { method } = req;
  // Ignora querystring para o roteamento.
  const url = (req.url || "").split("?")[0];

  // Responde ao preflight (OPTIONS) do navegador antes do POST.
  if (method === "OPTIONS") {
    aplicarCors(res);
    res.writeHead(204);
    return res.end();
  }

  // GET /api/status
  if (method === "GET" && url === "/api/status") {
    return enviarJson(res, 200, {
      status: "ok",
      mensagem: "Api funcionando",
    });
  }

  // GET /api/produtos
  if (method === "GET" && url === "/api/produtos") {
    return enviarJson(res, 200, PRODUTOS);
  }

  // POST /api/checkout
  if (method === "POST" && url === "/api/checkout") {
    // Node entrega os nomes de header em minúsculo.
    const chave = req.headers["apikey"];
    if (chave !== API_KEY) {
      console.log("✗ Checkout recusado: apiKey ausente ou inválida.");
      return enviarJson(res, 401, {
        status: false,
        message: "apiKey ausente ou inválida.",
      });
    }

    const body = await lerBody(req);
    const idPedido = proximoIdPedido++;

    // Imprime o pedido recebido no terminal.
    console.log("\n📦 Novo pedido recebido (#" + idPedido + ")");
    console.log("   status:", body.status);
    console.log("   produtos:", JSON.stringify(body.produtos));
    console.log("   totem: #" + body.totem_id +
      (body.totem_nome ? " — " + body.totem_nome : ""));

    return enviarJson(res, 200, {
      status: true,
      message: "Pedido cadastrado com sucesso.",
      id_pedido: idPedido,
    });
  }

  // Rota não encontrada.
  return enviarJson(res, 404, {
    status: false,
    message: "Rota não encontrada: " + method + " " + url,
  });
});

server.listen(PORT, () => {
  console.log("🍔 Mock server rodando em http://localhost:" + PORT + "/api");
  console.log("   GET  /api/status");
  console.log("   GET  /api/produtos");
  console.log("   POST /api/checkout   (header apiKey obrigatório)");
  console.log("\nPressione Ctrl+C para parar.\n");
});
