# cliente-pedidos

Sistema de pedidos em **HTML + CSS + JavaScript puro** com **Bootstrap 5** (via CDN).
São 5 telas navegáveis e o carrinho é persistido em `localStorage` (chave `"carrinho"`).
Acompanha um **mock server em Node.js puro** para testar sem o backend real.

## Telas

| Arquivo         | Função                                                                  |
| --------------- | ----------------------------------------------------------------------- |
| `setup.html`    | Configuração do totem (nº obrigatório + nome opcional). Porta de entrada. |
| `index.html`    | Tela inicial com logo e botão **Iniciar**.                              |
| `produtos.html` | Lista produtos da API, filtros por categoria (client-side), modal Add.  |
| `carrinho.html` | Itens do carrinho com **+ / − / ✕**, botões Cancelar e Confirmar pedido. |
| `checkout.html` | Revisão final e envio do pedido (POST `/checkout`).                     |
| `nota.html`     | Comprovante com número do pedido (`id_pedido`), itens e total.          |

## Estrutura

```
cliente-pedidos/
├── setup.html        ← configuração do totem (script inline, sem js/totem.js)
├── index.html
├── produtos.html
├── carrinho.html
├── checkout.html
├── nota.html
├── mock-server.js    ← servidor de teste (Node.js puro, porta 3001)
├── css/
│   └── style.css
└── js/
    ├── totem.js      ← identificação do totem (localStorage, window.Totem)
    ├── api.js        ← TODAS as chamadas fetch à API ficam aqui
    ├── cart.js       ← carrinho em localStorage (compartilhado)
    ├── produtos.js   ← carrega produtos, filtros, modal
    ├── carrinho.js   ← renderiza carrinho, controles de quantidade
    ├── checkout.js   ← monta resumo e envia o POST
    └── nota.js       ← lê o resultado do localStorage e exibe a nota
```

## Configuração da API

Toda a integração fica centralizada em [`js/api.js`](js/api.js). Para apontar
para outro backend, altere **apenas** a constante no topo do arquivo:

```js
// Mock server:        http://localhost:3001/api
// Backend CI4 real:   http://localhost/projeto-ci4/api
const API_BASE = "http://localhost:3001/api";
```

Endpoints usados:

- `GET  {API_BASE}/status`   — verifica se a API está no ar.
- `GET  {API_BASE}/produtos` — lista de produtos.
- `POST {API_BASE}/checkout` — registra o pedido. Inclui o header
  `apiKey: D41D8CD98F00B204E9800998ECF8427E`.

### Formato dos produtos (`GET /produtos`)

```json
[
  { "id": 1, "nome": "X-Burguer", "preco": 18.9, "tipo": "Lanches", "descricao": "...", "disponivel": 1 },
  { "id": 4, "nome": "Coca-Cola", "preco": 7.0, "tipo": "Bebidas", "descricao": "...", "disponivel": 1 }
]
```

> Os filtros **Todos / Lanches / Bebidas** comparam o campo `tipo`
> (sem acento, minúsculo), filtrando o array já carregado — sem nova requisição.

### Body do pedido (`POST /checkout`)

```json
{
  "status": "novo",
  "produtos": [{ "id_produto": 1, "quantidade": 2, "preco_unitario": 18.9 }]
}
```

Resposta de sucesso:

```json
{ "status": true, "message": "Pedido cadastrado com sucesso.", "id_pedido": 5 }
```

O `id_pedido` é gravado no `localStorage` e exibido em `nota.html`.

## Identificação do totem

Ao abrir o app pela primeira vez, o dispositivo é redirecionado para
`setup.html`, onde se informa o **número do totem** (obrigatório) e um **nome**
opcional (ex.: "Mesa 5", "Caixa 2"). Os valores ficam no `localStorage`
(`totem_id` / `totem_nome`) e acompanham cada pedido enviado
(`totem_id` e `totem_nome` no body do `POST /checkout`).

Todas as telas exigem o totem configurado; enquanto não estiver, redirecionam
para `setup.html`. O link **Reconfigurar totem** na tela inicial permite trocar
a identificação.

## Como rodar

### Rodar o mock server

```bash
node mock-server.js
# Requer Node.js (funciona em Windows e Mac)
```

Ele sobe em `http://localhost:3001/api` com CORS liberado e imprime cada
pedido recebido no terminal (inclusive o totem).

### Visualizar o frontend

Instalar a extensão **Live Server** no VS Code e abrir `index.html`,
**OU** rodar:

```bash
npx serve .
```

Acesso: `http://localhost:3000` (ou porta indicada). Servir por HTTP evita
problemas de CORS no `fetch`.

### Trocar para o backend real

Editar `js/api.js`:

```js
const API_BASE = "http://localhost:8080/api";  // php spark serve (Mac)
const API_BASE = "http://localhost/ProgWebIII/backend-pedidos/public/api"; // Laragon
```

## Próximas partes (preparação)

O código já está estruturado para as próximas entregas:

- `API_BASE` em `js/api.js` é a única constante a mudar entre ambientes.
- Há um `// TODO Parte 2` em `js/api.js` marcando onde entrará o token de
  autenticação nos headers.
- Pastas separadas (`js/` por responsabilidade) para facilitar a extensão.
