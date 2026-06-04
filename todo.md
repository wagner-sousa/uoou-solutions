# MVP — UOOU Solutions

> Centralizador de tarefas para o MVP. Atualize conforme progredimos.

---

## 🔴 1. Webhook Stripe & Gestão de Pedidos

- [ ] **Criar `src/Controller/WebhookController.php`** — rota `POST /webhook/stripe`
  - Validar assinatura com `STRIPE_WEBHOOK_SECRET`
  - Handler para `checkout.session.completed`
  - Handler para `product.updated`, `product.deleted`
- [ ] **Criar entidade `Order`** (`src/Entity/Order.php`):
  - `id`, `product` (ManyToOne), `quantity`, `total`, `stripeSessionId`, `status` (pending/completed/failed), `customerEmail`, `createdAt`
- [ ] **Criar entidade `OrderItem`** (`src/Entity/OrderItem.php`) — suporte a múltiplos itens por pedido
- [ ] **Criar `OrderRepository`** — consultas por status, por sessão Stripe
- [ ] **Baixar estoque no webhook** — ao receber `checkout.session.completed`, decrementar `stock_quantity` do produto
- [ ] **Criar migração** para tabelas `order` e `order_item`
- [ ] **Adicionar `STRIPE_WEBHOOK_SECRET`** no `.env` e documentar no `AGENTS.md`

## 🔴 2. Páginas de Erro Personalizadas

- [ ] **Criar `templates/bundles/TwigBundle/Exception/error404.html.twig`**
- [ ] **Criar `templates/bundles/TwigBundle/Exception/error403.html.twig`**
- [ ] **Criar `templates/bundles/TwigBundle/Exception/error500.html.twig`**
- [ ] **Criar `templates/bundles/TwigBundle/Exception/error.html.twig`** (fallback genérico)

## 🟡 3. Paginação na Listagem

- [ ] **Adicionar método `findPaginated(int $page, int $limit)`** no `ProductRepository`
- [ ] **Alterar `ProductController::index()`** para usar paginação (padrão: 15 por página)
- [ ] **Atualizar `index.html.twig`** com navegação de páginas (links anterior/próximo)
- [ ] **Adicionar contador "Mostrando X de Y produtos"**

## 🟡 4. Rate Limit & Resiliência no Stripe

- [ ] **Capturar `\Stripe\Exception\RateLimitException`** no `StripeService`
  - Implementar retry com exponential backoff (3 tentativas)
- [ ] **Capturar `\Stripe\Exception\ApiConnectionException`** — logar e exibir flash message amigável
- [ ] **Adicionar `STRIPE_RETRY_ATTEMPTS=3`** no `.env`

## 🟡 5. Índices no Banco de Dados

- [ ] **Criar migração** para adicionar index em `products.stripe_code`
- [ ] **Criar migração** para adicionar index em `products.stripe_price_id`
- [ ] **Criar migração** para adicionar index em `order.stripe_session_id`

## 🟡 6. Feedback Visual e Flash Messages

- [ ] **Adicionar flash messages** em todas as operações do `ProductController`:
  - "Produto criado com sucesso" (new)
  - "Produto atualizado com sucesso" (edit)
  - "Produto excluído com sucesso" (delete)
  - "Produto sincronizado com Stripe" (sync)
  - "Falha ao sincronizar com Stripe: {erro}" (sync error)
- [ ] **Adicionar flash message** no `StripeService` para erros de API
- [ ] **Adicionar toast notifications** via Stimulus + Bootstrap Toast (alternativa moderna aos alerts)

## 🟢 7. Estrutura & Navegação

- [ ] **Criar `HomeController`** — rota `/` redirecionando para `/products`
- [ ] **Adicionar link para o Dashboard do Stripe** no navbar (abre nova aba)
- [ ] **Configurar Turbo Drive** para navegação SPA (já instalado, ativar globalmente)
- [ ] **Criar `opencode.json`** na raiz com MCP do Stripe (já existe em `.vscode/`)

## 🟢 8. Validadores & Formulário

- [ ] **Adicionar constraints de validação** na entidade `Product`:
  - `#[NotBlank]` em `name`, `price`, `stockQuantity`
  - `#[PositiveOrZero]` em `price`, `stockQuantity`
  - `#[Length(max: 255)]` em `name`, `image`
- [ ] **Adicionar `required: true`** nos campos do formulário (`ProductType`)

## 🟢 9. Stripe — Configuração via Tela

- [ ] **Criar `StripeController`** com rotas:
  - `GET /admin/stripe` — formulário para inserir/editar a chave da Stripe
  - `POST /admin/stripe` — salva a chave em `.env.local` (ou session)
  - `POST /admin/stripe/test` — testa a conexão com a Stripe
- [ ] **Criar template** `templates/stripe/settings.html.twig`
- [ ] **Adicionar botão "Conectar Stripe"** que abre o Dashboard para copiar a chave

## 🟢 10. Middleware — Bloqueio por falta de Token

- [ ] **Criar `StripeCheckSubscriber`** (`EventSubscriber`)
  - Escuta `KernelEvents::REQUEST`
  - Se `StripeService` está em modo `offline` **e** não há chave configurada → redireciona para `/admin/stripe`
  - Exceto para rotas `/admin/stripe`, `_profiler`, `_wdt`, `/webhook/*`
- [ ] **Criar flash messages** orientando o usuário a configurar a Stripe

## 🟢 11. Stripe — Ícone de Sincronização

- [ ] **Adicionar coluna "Stripe"** na tabela de listagem (`index.html.twig`)
  - ✅ (verde) se `stripeProductId` e `stripePriceId` preenchidos
  - ❌ (vermelho) se não sincronizado
  - ⏳ (amarelo) se sync está pendente (modo async)
- [ ] **Adicionar indicador na página de detalhes** (`show.html.twig`)

## 🟢 12. Stripe — Envio Assíncrono (Eventos/Message)

- [ ] **Criar `Message/SyncProductMessage.php`**
- [ ] **Criar `MessageHandler/SyncProductHandler.php`** — chama `StripeService::syncProduct()`
- [ ] **Criar `Message/DeleteProductMessage.php`** — para deleção assíncrona
- [ ] **Criar `MessageHandler/DeleteProductHandler.php`**
- [ ] **Configurar rota do Messenger** em `messenger.yaml` para ambas as mensagens
- [ ] **Alterar `ProductController`** para despachar mensagens em vez de chamar o serviço diretamente
- [ ] **Adicionar comando `messenger:consume`** no `AGENTS.md`

## 🟢 13. Stripe — Importar Produtos

- [ ] **Criar `Command/ImportProductsCommand.php`** — `uoou:stripe:import`
  - Busca produtos ativos na Stripe via API (com paginação)
  - Cria entidades `Product` locais para cada um (se não existirem pelo `stripeProductId`)
- [ ] **Criar action no `StripeController`** (`POST /admin/stripe/import`) para importar via UI
- [ ] **Adicionar botão "Importar do Stripe"** na listagem de produtos
- [ ] **Adicionar barra de progresso** no import (via Turbo Stream ou polling)

## 🟢 14. Logs de Integração

- [ ] **Adicionar canal `stripe`** no `monolog.yaml`
- [ ] **Injetar `LoggerInterface`** no `StripeService` (canal `stripe`)
- [ ] **Adicionar logs** em todas as operações: sync, create, update, delete, checkout
- [ ] **Logar payload enviado** em modo debug
- [ ] **Logar resposta da Stripe** (status code, body resumido)
- [ ] **Logar erros com stack trace** no canal `stripe`

## 🟢 15. Modo Debug

- [ ] **Adicionar env `STRIPE_DEBUG=1`** — ativa modo debug
- [ ] **No `StripeService`**, quando debug ativo:
  - Logar payload completo antes de enviar
  - Exibir payload numa página de confirmação antes de enviar
- [ ] **Criar mecanismo de confirmação** via query param `?confirm=1` ou flash + botão
- [ ] **Adicionar badge "DEBUG"** no navbar quando modo ativo

## 🟢 16. Testes

- [ ] **Criar `tests/Service/StripeServiceTest.php`**:
  - Testar `shouldRunOffline()` em ambiente `test`
  - Testar `assignOfflineIds()`
  - Testar `createCheckoutSession()` em modo offline
  - Testar `syncProduct()` em modo offline
  - Testar `deleteProduct()` em modo offline
  - Testar debug mode (com `STRIPE_DEBUG=1`)
  - Testar rate limit retry (com mock)
- [ ] **Criar `tests/Controller/WebhookControllerTest.php`**:
  - Testar assinatura inválida → 400
  - Testar `checkout.session.completed` → baixa estoque
- [ ] **Criar `tests/MessageHandler/SyncProductHandlerTest.php`**
- [ ] **Atualizar `ProductControllerTest`**:
  - Testar que homepage (`/`) redireciona para `/products`
  - Testar validação de campos obrigatórios
  - Testar indicador de sync na listagem
- [ ] **Verificar** se `php bin/phpunit` passa limpo

## 🟢 17. Ajustes de Código

- [ ] **Remover `flush()` duplicado** no controller (se ainda existir)
- [ ] **Verificar** se `StripeService` funciona com injeção de dependência adequada
- [ ] **Configurar `config/packages/stripe.yaml`** para usar o `StripeClient` como serviço
- [ ] **Adicionar declare(strict_types=1)** em todos os arquivos PHP (padrão do projeto)

## 🔵 18. Makefile & DX

- [ ] **Criar `Makefile`** na raiz com comandos:

```makefile
  dev:          # php -S ou symfony serve
  test:         # php bin/phpunit
  migrate:      # php bin/console doctrine:migrations:migrate
  consume:      # php bin/console messenger:consume async -vv
  stripe:listen # stripe listen --forward-to http://localhost:8000/webhook/stripe
  build:        # npm run build
```

## 🔵 19. Deploy & Documentação

- [ ] **Criar `.env.example`** com todas as variáveis documentadas (sem secrets)
- [ ] **Criar `robots.txt`** (Disallow: /admin)
- [ ] **Criar `public/sitemap.xml`** básico
- [ ] **Adicionar health check** — `GET /health` → `{"status": "ok"}`
- [ ] **Criar `AGENTS.md`** com novos comandos, arquivos e fluxo de webhook
- [ ] **Verificar** se `.env.local` não contém chave real commitada
- [ ] **Rodar migrações** após alterações no banco

---

## ✅ Done

- [x] Criar estrutura inicial do projeto
- [x] Configurar Doctrine, Twig, Bootstrap, Stimulus, Turbo
- [x] CRUD de produtos com formulário
- [x] Integração básica com Stripe (offline/online)
- [x] Teste de índice e criação de produto
