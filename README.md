# UOOU SOLUTIONS

![](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)
![](https://img.shields.io/badge/Symfony_8.0-000000?style=for-the-badge&logo=symfony&logoColor=white)
![](https://img.shields.io/badge/Doctrine_ORM_3-000000?style=for-the-badge&logo=doctrine&logoColor=white)
![](https://img.shields.io/badge/Bootstrap_5-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![](https://img.shields.io/badge/Stripe-635BFF?style=for-the-badge&logo=stripe&logoColor=white)
![](https://img.shields.io/badge/PostgreSQL-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![](https://img.shields.io/badge/devcontainer-2753E3?style=for-the-badge&logo=developmentcontainers&logoColor=white)
![](https://img.shields.io/badge/nginx-009639?style=for-the-badge&logo=nginx&logoColor=white)
![](https://img.shields.io/badge/composer-885630?style=for-the-badge&logo=composer&logoColor=white)

## Sobre o Projeto

E-commerce Symfony com catálogo de produtos, integração Stripe (checkout + sincronização de produtos), modo offline para desenvolvimento, e testes automatizados.

### Páginas do E-commerce

O projeto deve contemplar duas páginas principais:

- **Catálogo de Produtos** - listagem com as informações de cada produto, incluindo: nome, descrição, imagem, preço, estoque, atributos e demais dados pertinentes.
- **Cadastro de Produtos** - formulário para cadastrar os produtos que serão exibidos no catálogo (página 1).


## Rotas

| Método | Rota | Nome | Ação |
|--------|------|------|------|
| `GET` | `/products` | `app_product_index` | Lista produtos |
| `GET/POST` | `/products/new` | `app_product_new` | Criar produto |
| `GET` | `/products/{id}` | `app_product_show` | Detalhes do produto |
| `GET/POST` | `/products/{id}/edit` | `app_product_edit` | Editar produto |
| `POST` | `/products/{id}` | `app_product_delete` | Excluir produto |
| `POST` | `/products/{id}/checkout` | `app_product_checkout` | Checkout Stripe |

## Integração com a Stripe

- Crie sua conta na Stripe e obtenha as credenciais de acesso.
- Os produtos cadastrados são sincronizados automaticamente com a Stripe.
- Na listagem de produtos, cada item deverá ter um botão "Comprar". Ao ser clicado, o produto é adicionado ao carrinho na Stripe, permitindo que o usuário prossiga com o processo de checkout diretamente pela Stripe.
- **Modo offline**: ativo em `APP_ENV=test` ou quando `STRIPE_OFFLINE=1`. Atribui IDs fictícios (`prod_test_*`, `price_test_*`) e retorna URLs simuladas.


## Funcionalidade Bônus (opcional, mas desejável)

Implemente um sistema de login/autenticação para proteger o acesso à página de cadastro de produtos, evitando que usuários não autorizados realizem inserções no catálogo.

### Comportamento offline vs online

| Condição | Comportamento |
|----------|--------------|
| `APP_ENV=test` | offline (testes nunca chamam Stripe) |
| `APP_ENV=dev` sem `STRIPE_SECRET_KEY` | offline |
| `APP_ENV=dev` com `STRIPE_SECRET_KEY` | online |
| `STRIPE_OFFLINE=1` | offline (forçado) |
| `STRIPE_OFFLINE=0` | online (forçado) |
| `APP_ENV=prod` | online (requer chave) |

## Pré-requisitos

> [!WARNING]
> Para modo online, você precisa de uma chave de API Stripe iniciada com `sk_`.

- [Obter chaves de API Stripe](https://support.stripe.com/questions/what-are-stripe-api-keys-and-how-to-find-them?locale=pt-BR)
- Docker (recomendado) ou PHP 8.4+ e PostgreSQL 16

## Devcontainer (recomendado)

O projeto inclui devcontainer com PHP 8.5, Nginx, PostgreSQL 16, Xdebug e Symfony CLI.

1. Abra o projeto no VS Code
2. Clique em **"Reopen in Container"** (ou `Ctrl+Shift+P` → `Dev Containers: Reopen in Container`)
3. O ambiente sobe automaticamente com banco, servidor PHP e Nginx
4. Acesse:
   - **Nginx:** http://localhost
   - **PHP direto:** http://localhost:8000

Dentro do container, execute os comandos de setup:

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
npm install && npm run build
```

> O servidor PHP já inicia automaticamente com `php -S 0.0.0.0:8000 -t public`.
> O Nginx está configurado como proxy reverso na porta 80.

## Instalação manual

```bash
cp .env .env.local   # configure DATABASE_URL e STRIPE_SECRET_KEY
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
npm install && npm run build
symfony serve
# Acesse: http://localhost:8000
```

### Frontend

```bash
npm install          # instala Bootstrap + esbuild
npm run build        # compila assets/bootstrap.js → public/build/
npm run dev          # watch mode com sourcemaps
```

- **`assets/bootstrap.js`** — entry point do esbuild (Bootstrap CSS + JS)
- **`assets/app.js`** — entry point do importmap (Stimulus)
- **`public/build/bootstrap.{js,css}`** — saída do esbuild
- Formulários usam o tema `bootstrap_5_layout.html.twig` do Symfony

## Ambiente

### Variáveis de ambiente

| Variável | Padrão | Descrição |
|----------|--------|-----------|
| `APP_ENV` | `dev` | Ambiente (`dev`, `test`, `prod`) |
| `DATABASE_URL` | `postgresql://...` | Conexão com banco |
| `STRIPE_SECRET_KEY` | — | Chave secreta Stripe (sk_) |
| `STRIPE_OFFLINE` | — | `1` para forçar modo offline |
| `STRIPE_DEBUG` | — | `1` para log detalhado das chamadas Stripe |

> **Nunca commite chaves reais.** Use `.env.local` para chaves locais (já ignorado pelo `.gitignore`).

### Banco de dados

| Ambiente | Driver | Configuração |
|----------|--------|-------------|
| Dev (`compose.yaml`) | PostgreSQL 16 | `postgresql://app:!ChangeMe!@database:5432/app` |
| Devcontainer | PostgreSQL 16 | Mesmo, via `.devcontainer/docker-compose.yml` |
| Teste | SQLite | `var/data_test.db` (recriado a cada execução) |

### Segurança

O `symfony/security-bundle` está instalado mas **sem user provider ou access control** configurados — a rota de cadastro não possui proteção no momento.

## Testes

```bash
php bin/phpunit
```

- PHPUnit 13 com flags `failOnDeprecation`, `failOnNotice`, `failOnWarning`
- Banco SQLite recriado a cada execução — sem migrações manuais
- `WebTestCase` limpa a tabela de produtos no `setUp()`

## Comandos úteis

| Ação | Comando |
|------|---------|
| Executar testes | `php bin/phpunit` |
| Listar rotas | `php bin/console debug:router` |
| Criar migration | `php bin/console make:migration` |
| Executar migration | `php bin/console doctrine:migrations:migrate` |
| Criar/atualizar entidade | `php bin/console make:entity` |
| Build frontend | `npm run build` |
| Dev frontend (watch) | `npm run dev` |

## Stripe MCP

O repositório possui configuração para [Stripe MCP](https://mcp.stripe.com):

> Veja [`todo.md`](todo.md) para detalhes e próximos passos.
