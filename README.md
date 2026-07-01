# Trabalho Final — Programação para Web III

Sistema de pedidos para totens de autoatendimento, dividido em três
subprojetos que se comunicam por uma API REST e compartilham um único
banco de dados MySQL (`pedidos_db`).

**Autor:** [2004ariel](https://github.com/2004ariel)

## Visão geral

| Papel                | Subprojeto                              | Tecnologia                         |
| --------------------- | ---------------------------------------- | ----------------------------------- |
| Cliente (totem)        | [`cliente-pedidos/`](cliente-pedidos)   | HTML + CSS + JavaScript puro        |
| Cozinha                | [`cozinha/`](cozinha)                   | CodeIgniter 4 (PHP)                 |
| Backend / Admin        | [`backend-pedidos/`](backend-pedidos)   | CodeIgniter 4 (PHP) + MySQL         |

### Fluxo do sistema

1. O cliente identifica o totem (`cliente-pedidos/setup.html`), monta o
   pedido e finaliza o checkout, que é enviado à API do backend.
2. O pedido cai na fila da **cozinha**, que o move entre os status
   `novo` → `em_preparo` → `finalizado` (ou `cancelado`).
3. O **backend** concentra autenticação, cadastro de produtos e usuários,
   e os relatórios administrativos (vendas por período, consumo por
   produto).

Todos os três subprojetos leem/escrevem no mesmo banco `pedidos_db`; é o
`backend-pedidos` que possui as migrations e é responsável por criar o
schema (tabelas `usuarios`, `produtos`, `pedidos`, `pedido_produtos`).

## Tecnologias utilizadas

- **PHP 8.4** com **CodeIgniter 4** (backend e cozinha)
- **MySQL** (banco `pedidos_db` compartilhado)
- **HTML, CSS e JavaScript puro** com **Bootstrap 5** (via CDN) no cliente
- **Chart.js** (via CDN) para o gráfico do painel de vendas
- **Node.js** (mock server, apenas para desenvolvimento do frontend sem o
  backend real no ar)

## Como rodar o projeto completo

Cada subprojeto tem seu próprio `README.md` com instruções detalhadas de
instalação (Windows/Laragon e Mac/Linux). Ordem recomendada:

1. **[`backend-pedidos/`](backend-pedidos/README.md)** — instalar
   dependências, configurar `.env`, criar o banco e rodar
   migrations/seeds. É este projeto que cria o schema do `pedidos_db`.
2. **[`cozinha/`](cozinha/README.md)** — instalar dependências e
   configurar `.env` apontando para o mesmo `pedidos_db` (não roda
   migrations, apenas lê as tabelas já criadas pelo backend).
3. **[`cliente-pedidos/`](cliente-pedidos/README.md)** — abrir com
   Live Server/`npx serve` ou apontar `js/api.js` para o backend real
   (`http://localhost:8080/api` no Mac, ou o caminho do Laragon no
   Windows).

## Credenciais de teste (backend-pedidos)

Criadas pelos seeds do backend, senha `123456` para todos:

| E-mail               | Perfil     |
| --------------------- | ---------- |
| `super@pedidos.com`   | superadmin |
| `admin@pedidos.com`   | admin      |
| `user@pedidos.com`    | usuario    |

## Estrutura do repositório

```
ProgWebIII/
├── cliente-pedidos/   ← app do totem (HTML/CSS/JS puro)
├── cozinha/            ← painel da cozinha (CodeIgniter 4)
└── backend-pedidos/    ← autenticação, admin e API (CodeIgniter 4)
```
