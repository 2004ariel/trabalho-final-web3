# backend-pedidos

Backend principal do sistema (CodeIgniter 4): autenticação, controle de
usuários, catálogo de produtos e painéis de vendas/consumo para admins.

É este projeto que **possui o banco `pedidos_db`** — as migrations e seeds
abaixo criam as tabelas `usuarios`, `produtos`, `pedidos` e
`pedido_produtos` usadas também por `cozinha` e `cliente-pedidos`.

## Clonar e instalar

```bash
git clone <repo>
cd backend-pedidos
composer install
cp .env.example .env
```

## Configurar o .env

Ajuste a seção `database` do `.env` conforme o seu ambiente local (usuário/
senha do MySQL podem variar). Os valores padrão apontam para `pedidos_db`
em `localhost` com usuário `root` sem senha.

## Criar o banco e rodar migrations/seeds

```bash
php spark db:create pedidos_db
php spark migrate
php spark db:seed UsuarioSeeder
php spark db:seed ProdutoSeeder
php spark db:seed PedidoSeeder
```

Os seeds criam três usuários de teste (senha `123456` para todos):

| E-mail               | Tipo       |
| --------------------- | ---------- |
| `super@pedidos.com`   | superadmin |
| `admin@pedidos.com`   | admin      |
| `user@pedidos.com`    | usuario    |

## Rodar (Mac/Linux)

```bash
php spark serve
```

Acesso: http://localhost:8080/login

## Rodar (Windows com Laragon)

Coloque a pasta em `C:\laragon\www\ProgWebIII\backend-pedidos` e ajuste no
`.env`:

```
app.baseURL = http://localhost/ProgWebIII/backend-pedidos/public/
```

Acesso: http://localhost/ProgWebIII/backend-pedidos/public/login

## Controle de acesso

| Perfil     | Acessa                                          |
| ---------- | ------------------------------------------------ |
| usuario    | Produtos, edição do próprio cadastro              |
| admin      | + Painel de vendas, consumo por produto           |
| superadmin | + Cadastro/edição/bloqueio de usuários            |

Usuários com `bloqueado = 1` não conseguem fazer login (mensagem de erro
exibida na tela).

## Rotas principais

| Método | Rota                              | Ação                                   |
| ------ | ---------------------------------- | --------------------------------------- |
| GET    | `login`                            | Formulário de login                     |
| POST   | `login`                            | Autentica                               |
| GET    | `logout`                           | Encerra a sessão                        |
| GET    | `produtos`                         | Lista de produtos (autenticado)         |
| GET    | `usuarios/editar/{id}`             | Editar o próprio cadastro               |
| POST   | `usuarios/atualizar/{id}`          | Atualiza o próprio cadastro             |
| GET    | `admin/usuarios`                   | Lista de usuários (superadmin)          |
| GET    | `admin/usuarios/criar`             | Form de novo usuário (superadmin)       |
| POST   | `admin/usuarios/salvar`            | Salva novo usuário (superadmin)         |
| GET    | `admin/usuarios/bloquear/{id}`     | Bloqueia/desbloqueia (superadmin)       |
| GET    | `admin/relatorios/vendas`          | Painel de vendas (admin/superadmin)     |
| GET    | `admin/relatorios/consumo`         | Consumo por produto (admin/superadmin)  |
