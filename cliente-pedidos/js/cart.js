/* =============================================================
 * cart.js — Carrinho persistido em localStorage
 * -------------------------------------------------------------
 * Funções utilitárias compartilhadas por todas as telas.
 * Chave do carrinho: "carrinho"
 * Estrutura de um item:
 *   { id, nome, preco, quantidade, tipo }
 * ============================================================= */

(function () {
  "use strict";

  const STORAGE_KEY = "carrinho";

  // -----------------------------------------------------------
  // Leitura / escrita base
  // -----------------------------------------------------------
  function getCart() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      const data = raw ? JSON.parse(raw) : [];
      return Array.isArray(data) ? data : [];
    } catch (_) {
      return [];
    }
  }

  function saveCart(itens) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(itens || []));
  }

  function clearCart() {
    localStorage.removeItem(STORAGE_KEY);
  }

  // -----------------------------------------------------------
  // Operações
  // -----------------------------------------------------------

  /**
   * Adiciona um produto ao carrinho. Se já existir, soma a quantidade.
   * @param {Object} produto  { id, nome, preco, tipo }
   * @param {number} quantidade
   */
  function addItem(produto, quantidade = 1) {
    const qtd = Math.max(1, parseInt(quantidade, 10) || 1);
    const itens = getCart();
    const existente = itens.find((i) => String(i.id) === String(produto.id));

    if (existente) {
      existente.quantidade += qtd;
    } else {
      itens.push({
        id: produto.id,
        nome: produto.nome,
        preco: Number(produto.preco) || 0,
        quantidade: qtd,
        tipo: produto.tipo || "",
      });
    }
    saveCart(itens);
    return itens;
  }

  /** Aumenta em 1 a quantidade de um item. */
  function increment(id) {
    const itens = getCart();
    const item = itens.find((i) => String(i.id) === String(id));
    if (item) {
      item.quantidade += 1;
      saveCart(itens);
    }
    return itens;
  }

  /** Diminui em 1; remove o item se chegar a zero. */
  function decrement(id) {
    let itens = getCart();
    const item = itens.find((i) => String(i.id) === String(id));
    if (item) {
      item.quantidade -= 1;
      if (item.quantidade <= 0) {
        itens = itens.filter((i) => String(i.id) !== String(id));
      }
      saveCart(itens);
    }
    return itens;
  }

  /** Remove completamente um item do carrinho. */
  function removeItem(id) {
    const itens = getCart().filter((i) => String(i.id) !== String(id));
    saveCart(itens);
    return itens;
  }

  // -----------------------------------------------------------
  // Cálculos
  // -----------------------------------------------------------

  /** Quantidade total de unidades no carrinho. */
  function count() {
    return getCart().reduce((acc, i) => acc + i.quantidade, 0);
  }

  /** Valor total do carrinho. */
  function total() {
    return getCart().reduce((acc, i) => acc + i.preco * i.quantidade, 0);
  }

  /** Formata um número para moeda brasileira (R$). */
  function formatBRL(valor) {
    return (Number(valor) || 0).toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    });
  }

  // -----------------------------------------------------------
  // Nota (resultado do checkout) — persistida para a tela nota.html
  // É assim que o id_pedido é passado de checkout.html para nota.html.
  // -----------------------------------------------------------
  const NOTA_KEY = "nota";

  function saveNota(nota) {
    localStorage.setItem(NOTA_KEY, JSON.stringify(nota || {}));
  }

  function getNota() {
    try {
      const raw = localStorage.getItem(NOTA_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch (_) {
      return null;
    }
  }

  function clearNota() {
    localStorage.removeItem(NOTA_KEY);
  }

  // -----------------------------------------------------------
  // Exposição global
  // -----------------------------------------------------------
  window.Cart = {
    getCart,
    saveCart,
    clearCart,
    addItem,
    increment,
    decrement,
    removeItem,
    count,
    total,
    formatBRL,
    saveNota,
    getNota,
    clearNota,
  };
})();
