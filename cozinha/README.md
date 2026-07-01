# cozinha

Interface da cozinha (CodeIgniter 4). Exibe os pedidos pendentes (`novo` e
`em_preparo`) e permite iniciar o preparo, finalizar ou cancelar cada pedido.

Usa o mesmo banco `pedidos_db` do projeto `backend-pedidos` (tabelas
`pedidos`, `pedido_produtos`, `produtos` já existentes — este projeto não
cria migrations).

## Clonar e instalar

```bash
git clone <repo>
cd cozinha
composer install
cp .env.example .env
```

`composer install` já cria as subpastas de `writable/` (cache, logs, sessão
etc.) necessárias para o CodeIgniter rodar — elas não ficam versionadas no
git.

## Configurar o .env

Abra o `.env` recém-criado e ajuste a seção `database` conforme o seu
ambiente local (usuário/senha do MySQL podem variar). Os valores padrão já
apontam para `pedidos_db` em `localhost` com usuário `root` sem senha.

## Rodar (Mac/Linux)

⚠️ O `backend-pedidos` já ocupa a porta padrão (8080) com `php spark serve`.
Como os dois projetos rodam ao mesmo tempo, suba a cozinha em outra porta e
ajuste o `.env` pra combinar:

```bash
# no .env, altere:
app.baseURL = http://localhost:8081/

php spark serve --port 8081
```

Acesso: http://localhost:8081/cozinha

## Rodar (Windows com Laragon)

Coloque a pasta em `C:\laragon\www\ProgWebIII\cozinha` e ajuste no `.env`:

```
app.baseURL = http://localhost/ProgWebIII/cozinha/public/
```

Acesso: http://localhost/ProgWebIII/cozinha/public/

## Rotas

| Método | Rota                       | Ação                                |
| ------ | -------------------------- | ------------------------------------ |
| GET    | `/`, `cozinha`             | Lista de pedidos pendentes           |
| GET    | `cozinha/detalhes/{id}`    | Detalhes de um pedido                |
| POST   | `cozinha/status/{id}`      | Atualiza o status do pedido          |
