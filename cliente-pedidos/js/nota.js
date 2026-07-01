/* =============================================================
 * nota.js — Tela da nota / comprovante
 * -------------------------------------------------------------
 * Lê a nota salva no localStorage pelo checkout e a exibe no
 * formato de cupom. O botão "Novo pedido" limpa carrinho e nota.
 * ============================================================= */

(function () {
  "use strict";

  function formatarData(iso) {
    const d = iso ? new Date(iso) : new Date();
    return d.toLocaleString("pt-BR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  }

  function render() {
    const nota = Cart.getNota();
    const $vazio = document.getElementById("vazio");
    const $nota = document.getElementById("nota");

    if (!nota || !Array.isArray(nota.itens) || nota.itens.length === 0) {
      $vazio.classList.remove("d-none");
      return;
    }

    $nota.classList.remove("d-none");
    document.getElementById("nota-numero").textContent = nota.id_pedido;
    document.getElementById("nota-data").textContent = formatarData(nota.data);
    document.getElementById("nota-total").textContent = Cart.formatBRL(
      nota.total
    );

    const $itens = document.getElementById("nota-itens");
    $itens.innerHTML = "";
    nota.itens.forEach((item) => {
      const li = document.createElement("li");
      li.className = "d-flex justify-content-between mb-1";
      li.innerHTML = `
        <span>${item.quantidade}× ${item.nome}</span>
        <span>${Cart.formatBRL(
          item.subtotal != null ? item.subtotal : item.preco * item.quantidade
        )}</span>`;
      $itens.appendChild(li);
    });
  }

  // "Novo pedido" — limpa carrinho e nota, depois volta aos produtos.
  function configurarNovoPedido() {
    const $btn = document.getElementById("btn-novo");
    if (!$btn) return;
    $btn.addEventListener("click", () => {
      Cart.clearCart();
      Cart.clearNota();
      window.location.href = "produtos.html";
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    if (!Totem.isSetup()) {
      window.location.href = "setup.html";
      return;
    }
    render();
    configurarNovoPedido();
  });
})();
