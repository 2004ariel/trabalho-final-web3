/* =============================================================
 * carrinho.js — Tela do carrinho
 * - Lista itens com controles + / - / X
 * - Botões Cancelar (limpa) e Confirmar (vai para o checkout)
 * ============================================================= */

(function () {
  "use strict";

  const $lista = document.getElementById("lista-itens");
  const $total = document.getElementById("total");
  const $vazio = document.getElementById("vazio");
  const $conteudo = document.getElementById("conteudo");

  // -----------------------------------------------------------
  // Render
  // -----------------------------------------------------------
  function render() {
    const itens = Cart.getCart();

    if (itens.length === 0) {
      $vazio.classList.remove("d-none");
      $conteudo.classList.add("d-none");
      return;
    }
    $vazio.classList.add("d-none");
    $conteudo.classList.remove("d-none");

    $lista.innerHTML = "";
    itens.forEach((item) => {
      const li = document.createElement("li");
      li.className =
        "list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2";
      li.innerHTML = `
        <div class="me-auto">
          <div class="fw-semibold">${item.nome}</div>
          <small class="text-secondary">${Cart.formatBRL(item.preco)} cada</small>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm" data-acao="menos">−</button>
          <span class="qtd-control">${item.quantidade}</span>
          <button class="btn btn-outline-secondary btn-sm" data-acao="mais">+</button>
          <span class="fw-semibold ms-2" style="min-width:5rem;text-align:right">
            ${Cart.formatBRL(item.preco * item.quantidade)}
          </span>
          <button class="btn btn-outline-danger btn-sm ms-2" data-acao="remover" title="Remover">✕</button>
        </div>`;

      li.querySelector('[data-acao="menos"]').addEventListener("click", () => {
        Cart.decrement(item.id);
        render();
      });
      li.querySelector('[data-acao="mais"]').addEventListener("click", () => {
        Cart.increment(item.id);
        render();
      });
      li.querySelector('[data-acao="remover"]').addEventListener("click", () => {
        Cart.removeItem(item.id);
        render();
      });

      $lista.appendChild(li);
    });

    $total.textContent = Cart.formatBRL(Cart.total());
  }

  // -----------------------------------------------------------
  // Ações
  // -----------------------------------------------------------
  function configurarBotoes() {
    // Cancelar — volta para a tela de produtos (mantém o carrinho).
    document.getElementById("btn-cancelar").addEventListener("click", () => {
      window.location.href = "produtos.html";
    });

    // Confirmar pedido — segue para a revisão / checkout.
    document.getElementById("btn-confirmar").addEventListener("click", () => {
      if (Cart.count() === 0) return;
      window.location.href = "checkout.html";
    });
  }

  // -----------------------------------------------------------
  // Init
  // -----------------------------------------------------------
  document.addEventListener("DOMContentLoaded", () => {
    if (!Totem.isSetup()) {
      window.location.href = "setup.html";
      return;
    }
    configurarBotoes();
    render();
  });
})();
