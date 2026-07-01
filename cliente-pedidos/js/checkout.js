/* =============================================================
 * checkout.js — Tela de revisão e envio do pedido
 * -------------------------------------------------------------
 * - Mostra o resumo final (somente leitura)
 * - Monta o body no formato do backend e faz POST /checkout
 *   (header apiKey, via API.postCheckout)
 * - Salva a nota no localStorage e segue para nota.html
 * ============================================================= */

(function () {
  "use strict";

  const $lista = document.getElementById("lista-itens");
  const $total = document.getElementById("total");
  const $vazio = document.getElementById("vazio");
  const $conteudo = document.getElementById("conteudo");
  const $erro = document.getElementById("erro");
  const $btn = document.getElementById("btn-enviar");

  // -----------------------------------------------------------
  // Render do resumo (somente leitura)
  // -----------------------------------------------------------
  function render() {
    const itens = Cart.getCart();

    if (itens.length === 0) {
      $vazio.classList.remove("d-none");
      $conteudo.classList.add("d-none");
      return;
    }

    $lista.innerHTML = "";
    itens.forEach((item) => {
      const li = document.createElement("li");
      li.className =
        "list-group-item d-flex justify-content-between align-items-center";
      li.innerHTML = `
        <div>
          <span class="fw-semibold">${item.quantidade}×</span> ${item.nome}
          <small class="text-secondary d-block">${Cart.formatBRL(
            item.preco
          )} cada</small>
        </div>
        <span class="fw-semibold">${Cart.formatBRL(
          item.preco * item.quantidade
        )}</span>`;
      $lista.appendChild(li);
    });

    $total.textContent = Cart.formatBRL(Cart.total());
  }

  // -----------------------------------------------------------
  // Envio do pedido
  // -----------------------------------------------------------
  async function enviar() {
    const itens = Cart.getCart();
    if (itens.length === 0) return;

    $erro.classList.add("d-none");
    $btn.disabled = true;
    $btn.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span>Enviando…';

    // Body no formato exigido pelo backend:
    // { status: "novo", produtos: [{ id_produto, quantidade, preco_unitario }] }
    const pedido = {
      status: "novo",
      totem_id: Totem.getId(),
      totem_nome: Totem.getNome(),
      produtos: itens.map((i) => ({
        id_produto: i.id,
        quantidade: i.quantidade,
        preco_unitario: Number(i.preco),
      })),
    };

    try {
      const resposta = await API.postCheckout(pedido);

      // O backend devolve { status: true, message, id_pedido }.
      if (resposta && resposta.status === false) {
        throw new Error(resposta.message || "Não foi possível registrar o pedido.");
      }

      // Monta a nota a partir do carrinho + id_pedido retornado.
      const nota = {
        id_pedido:
          (resposta && resposta.id_pedido) != null
            ? resposta.id_pedido
            : gerarNumeroLocal(),
        itens: itens.map((i) => ({
          nome: i.nome,
          quantidade: i.quantidade,
          preco: i.preco,
          subtotal: Number((i.preco * i.quantidade).toFixed(2)),
        })),
        total: Number(Cart.total().toFixed(2)),
        data: new Date().toISOString(),
      };

      // Passa os dados para a próxima página via localStorage.
      Cart.saveNota(nota);
      Cart.clearCart();
      window.location.href = "nota.html";
    } catch (err) {
      // Erro de rede / backend tratado com mensagem amigável na tela.
      $erro.textContent = err.message;
      $erro.classList.remove("d-none");
      $btn.disabled = false;
      $btn.textContent = "Confirmar e enviar";
    }
  }

  // Número de fallback caso o backend não retorne um id_pedido.
  function gerarNumeroLocal() {
    return "LOCAL-" + Date.now().toString().slice(-6);
  }

  // -----------------------------------------------------------
  // Init
  // -----------------------------------------------------------
  document.addEventListener("DOMContentLoaded", () => {
    if (!Totem.isSetup()) {
      window.location.href = "setup.html";
      return;
    }
    render();
    $btn.addEventListener("click", enviar);
  });
})();
