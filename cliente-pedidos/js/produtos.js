/* =============================================================
 * produtos.js — Tela de listagem de produtos
 * -------------------------------------------------------------
 * - Carrega produtos da API em UMA única requisição (GET /produtos)
 * - Filtra por categoria (tipo) no client-side, sem nova requisição
 * - Modal de quantidade com total do item em tempo real
 * ============================================================= */

(function () {
  "use strict";

  let todosProdutos = []; // cache da resposta da API
  let filtroAtual = "todos";
  let produtoSelecionado = null;
  let quantidadeModal = 1;
  let modalInstance = null;

  // Elementos da página
  const $lista = document.getElementById("lista-produtos");
  const $loading = document.getElementById("loading");
  const $erro = document.getElementById("erro");
  const $vazio = document.getElementById("vazio");
  const $filtros = document.getElementById("filtros");

  // Emoji ilustrativo (placeholder) por tipo de produto.
  function emojiPara(tipo) {
    const t = normalizar(tipo);
    if (t.includes("bebida")) return "🥤";
    if (t.includes("porc") || t.includes("porç")) return "🍟";
    if (t.includes("sobrem")) return "🍰";
    return "🌭"; // lanches / padrão — a estrela da casa
  }

  // -----------------------------------------------------------
  // Normaliza texto para comparação (sem acento, minúsculo)
  // -----------------------------------------------------------
  function normalizar(texto) {
    return String(texto || "")
      .toLowerCase()
      .normalize("NFD")
      .replace(/[̀-ͯ]/g, "")
      .trim();
  }

  // -----------------------------------------------------------
  // Atualiza o indicador de total/contagem do carrinho na barra
  // -----------------------------------------------------------
  function atualizarResumoCarrinho() {
    document.getElementById("cart-total").textContent = Cart.formatBRL(
      Cart.total()
    );

    const count = Cart.count();
    const $count = document.getElementById("cart-count");
    if (count > 0) {
      $count.textContent = count;
      $count.classList.remove("d-none");
    } else {
      $count.classList.add("d-none");
    }
  }

  // -----------------------------------------------------------
  // Renderiza a grade de acordo com o filtro atual
  // -----------------------------------------------------------
  function renderizar() {
    const filtrados =
      filtroAtual === "todos"
        ? todosProdutos
        : todosProdutos.filter((p) => normalizar(p.tipo) === filtroAtual);

    $lista.innerHTML = "";

    if (filtrados.length === 0) {
      $vazio.classList.remove("d-none");
      return;
    }
    $vazio.classList.add("d-none");

    filtrados.forEach((p) => {
      const indisponivel = Number(p.disponivel) === 0;
      const col = document.createElement("div");
      col.className = "col";
      col.innerHTML = `
        <div class="card produto-card shadow-sm">
          <div class="produto-thumb">${emojiPara(p.tipo)}</div>
          <div class="card-body d-flex flex-column">
            <span class="badge text-bg-light align-self-start mb-2">${
              p.tipo || "—"
            }</span>
            <h6 class="card-title">${p.nome}</h6>
            ${
              p.descricao
                ? `<p class="card-text small text-secondary">${p.descricao}</p>`
                : ""
            }
            <div class="mt-auto d-flex justify-content-between align-items-center pt-2">
              <span class="produto-preco">${Cart.formatBRL(p.preco)}</span>
              <button class="btn btn-sm btn-brand" ${
                indisponivel ? "disabled" : ""
              }>${indisponivel ? "Indisponível" : "Adicionar"}</button>
            </div>
          </div>
        </div>`;

      if (!indisponivel) {
        col
          .querySelector("button")
          .addEventListener("click", () => abrirModal(p));
      }
      $lista.appendChild(col);
    });
  }

  // -----------------------------------------------------------
  // Modal de quantidade
  // -----------------------------------------------------------
  function atualizarTotalModal() {
    document.getElementById("modal-qtd").textContent = quantidadeModal;
    const total = (Number(produtoSelecionado.preco) || 0) * quantidadeModal;
    document.getElementById("modal-total").textContent = Cart.formatBRL(total);
  }

  function abrirModal(produto) {
    produtoSelecionado = produto;
    quantidadeModal = 1;
    document.getElementById("modal-produto-nome").textContent = produto.nome;
    document.getElementById("modal-produto-preco").textContent =
      Cart.formatBRL(produto.preco) + " cada";
    atualizarTotalModal();
    modalInstance.show();
  }

  function configurarModal() {
    modalInstance = new bootstrap.Modal(document.getElementById("modalQtd"));

    document.getElementById("modal-mais").addEventListener("click", () => {
      quantidadeModal += 1;
      atualizarTotalModal();
    });

    document.getElementById("modal-menos").addEventListener("click", () => {
      if (quantidadeModal > 1) {
        quantidadeModal -= 1;
        atualizarTotalModal();
      }
    });

    document.getElementById("modal-add").addEventListener("click", () => {
      if (produtoSelecionado) {
        Cart.addItem(produtoSelecionado, quantidadeModal);
        atualizarResumoCarrinho();
      }
      modalInstance.hide();
    });
  }

  // -----------------------------------------------------------
  // Filtros (client-side — reutilizam o cache, sem nova requisição)
  // -----------------------------------------------------------
  function configurarFiltros() {
    $filtros.addEventListener("click", (e) => {
      const btn = e.target.closest(".filtro-btn");
      if (!btn) return;

      $filtros
        .querySelectorAll(".filtro-btn")
        .forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      filtroAtual = btn.dataset.cat;
      renderizar();
    });
  }

  // -----------------------------------------------------------
  // Carregamento inicial
  // -----------------------------------------------------------
  async function carregar() {
    try {
      todosProdutos = await API.getProdutos();
      $loading.classList.add("d-none");
      renderizar();
    } catch (err) {
      // Erro de rede tratado com mensagem amigável na própria tela.
      $loading.classList.add("d-none");
      $erro.textContent = err.message;
      $erro.classList.remove("d-none");
    }
  }

  // -----------------------------------------------------------
  // Init
  // -----------------------------------------------------------
  document.addEventListener("DOMContentLoaded", () => {
    if (!Totem.isSetup()) {
      window.location.href = "setup.html";
      return;
    }
    configurarModal();
    configurarFiltros();
    atualizarResumoCarrinho();
    carregar();
  });
})();
