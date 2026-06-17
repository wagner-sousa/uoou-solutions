When performing a code review, always respond in Brazilian Portuguese (pt-BR).

## Projeto
- Symfony 8.0 + PHP 8.4 + Doctrine ORM 3 + Stripe
- Frontend: Bootstrap 5 (esbuild), Stimulus 3, Turbo 7
- Testes: PHPUnit 13, WebTestCase, SQLite (recriado a cada execução)

## Regras de revisão
1. IDIOMA: Todos os comentários da review devem ser em português brasileiro.
2. ENTITIES: Use atributos PHP 8 (`#[ORM\Column]`). Setters devem retornar `static` (fluent interface). Getters retornam tipo nullable (`?type`).
3. CONTROLLERS: Classes `final` com `AbstractController`. Use `#[Route]` de atributo, injeção de serviço no construtor, e `Response::HTTP_SEE_OTHER` (303) em redirects.
4. FORMS: Estenda `AbstractType`. Use `MoneyType` com `currency: 'BRL'`. Labels em português.
5. STRIPE: StripeService tem modo offline (checa `APP_ENV=test` ou `STRIPE_OFFLINE=1`). IDs do Stripe só persistem após chamada de API bem-sucedida.
6. SEGURANÇA: Validar CSRF em mutations. Não expor IDs internos nas URLs se houver risco. Não commitar chaves Stripe.
7. TESTES: Use `WebTestCase`. Clean DB em `setUp()` deletando registros. Submissão de formulário com chaves no formato `product[nome]`.
8. BOAS PRÁTICAS: Evite `findAll()` sem paginação. Prefira tipos PHP 8.4 (typed properties, readonly se aplicável). Use `match` ou `enum` quando apropriado.

