# MVP — UOOU Solutions

> Centralizador de tarefas para o MVP. Atualize conforme progredimos.

---

## 1. Estrutura & Navegação

- [ ] **Criar `HomeController`** — rota `/` redirecionando para `/products`
- [ ] **Adicionar link para o Dashboard do Stripe** no navbar (abre nova aba)
- [ ] **Configurar Turbo Drive** para navegação SPA (já instalado, ativar globalmente)
- [ ] **Criar `opencode.json`** na raiz com MCP do Stripe (já existe em `.vscode/`)

## 2. Validadores & Formulário

- [ ] **Adicionar constraints de validação** na entidade `Product`:
  - `#[NotBlank]` em `name`, `price`, `stockQuantity`
  - `#[PositiveOrZero]` em `price`, `stockQuantity`
  - `#[Length(max: 255)]` em `name`, `image`
- [ ] **Adicionar `required: true`** nos campos do formulário (`ProductType`)

## 3. Stripe — Configuração via Tela

- [ ] **Criar `StripeController`** com rotas:
  - `GET /admin/stripe` — formulário para inserir/editar a chave da Stripe
  - `POST /admin/stripe` — salva a chave em `.env.local` (ou session)
  - `POST /admin/stripe/test` — testa a conexão com a Stripe
- [ ] **Criar template** `templates/stripe/settings.html.twig`
- [ ] **Adicionar botão "Conectar Stripe"** que abre o Dashboard para copiar a chave

## 4. Middleware — Bloqueio por falta de Token

- [ ] **Criar `StripeCheckSubscriber`** (`EventSubscriber`)
  - Escuta `KernelEvents::REQUEST`
  - Se `StripeService` está em modo `offline` **e** não há chave configurada → redireciona para `/admin/stripe`
  - Exceto para rotas `/admin/stripe`, `_profiler`, `_wdt`
- [ ] **Criar flash messages** orientando o usuário a configurar a Stripe

## 5. Stripe — Ícone de Sincronização

- [ ] **Adicionar coluna "Stripe"** na tabela de listagem (`index.html.twig`)
  - ✅ (verde) se `stripeProductId` e `stripePriceId` preenchidos
  - ❌ (vermelho) se não sincronizado
- [ ] **Adicionar indicador na página de detalhes** (`show.html.twig`)

## 6. Stripe — Envio Assíncrono (Eventos/Message)

- [ ] **Criar `Message/SyncProductMessage.php`**
- [ ] **Criar `MessageHandler/SyncProductHandler.php`** — chama `StripeService::syncProduct()`
- [ ] **Configurar rota do Messenger** em `messenger.yaml`
- [ ] **Alterar `ProductController`** para despachar a mensagem em vez de chamar o serviço diretamente

## 7. Stripe — Importar Produtos

- [ ] **Criar `Command/ImportProductsCommand.php`** — `uoou:stripe:import`
  - Busca produtos ativos na Stripe via API
  - Cria entidades `Product` locais para cada um (se não existirem pelo `stripeProductId`)
- [ ] **Criar action no `StripeController`** (`POST /admin/stripe/import`) para importar via UI
- [ ] **Adicionar botão "Importar do Stripe"** na listagem de produtos

## 8. Logs de Integração

- [ ] **Adicionar canal `stripe`** no `monolog.yaml`
- [ ] **Injetar `LoggerInterface`** no `StripeService` (canal `stripe`)
- [ ] **Adicionar logs** em todas as operações: sync, create, update, delete, checkout
- [ ] **Logar payload enviado** em modo debug

## 9. Modo Debug

- [ ] **Adicionar env `STRIPE_DEBUG=1`** — ativa modo debug
- [ ] **No `StripeService`**, quando debug ativo:
  - Logar payload completo antes de enviar
  - Se não houver confirmação explícita, lançar exceção em vez de enviar
- [ ] **Criar mecanismo de confirmação** via query param `?confirm=1` ou flash + botão
- [ ] **Adicionar badge "DEBUG"** no navbar quando modo ativo

## 10. Testes

- [ ] **Criar `tests/Service/StripeServiceTest.php`**:
  - Testar `shouldRunOffline()` em ambiente `test`
  - Testar `assignOfflineIds()`
  - Testar `createCheckoutSession()` em modo offline
  - Testar `syncProduct()` em modo offline
  - Testar `deleteProduct()` em modo offline
- [ ] **Atualizar `ProductControllerTest`**:
  - Testar que homepage (`/`) redireciona para `/products`
  - Testar validação de campos obrigatórios
  - Testar indicador de sync na listagem
- [ ] **Verificar** se `php bin/phpunit` passa limpo

## 11. Ajustes de Código

- [ ] **Remover `flush()` duplicado** no controller (se ainda existir)
- [ ] **Verificar** se `StripeService` funciona com injeção de dependência adequada
- [ ] **Configurar `config/packages/stripe.yaml`** para usar o `StripeClient` como serviço

## 12. Deploy & Documentação

- [ ] **Atualizar `AGENTS.md`** com novos comandos e arquivos
- [ ] **Verificar** se `.env.local` não contém chave real commitada
- [ ] **Rodar migrações** após alterações no banco

---

## ✅ Done

- [x] Criar estrutura inicial do projeto
- [x] Configurar Doctrine, Twig, Bootstrap, Stimulus, Turbo
- [x] CRUD de produtos com formulário
- [x] Integração básica com Stripe (offline/online)
- [x] Teste de índice e criação de produto
