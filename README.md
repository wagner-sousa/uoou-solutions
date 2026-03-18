# UOOU SOLUTIONS

Desenvolver um e-commerce

## Stacks
- PHP com o framework [Symfony](https://symfony.com/).
- Templates: utilize [Twig](https://twig.symfony.com/) para a camada de apresentação.
- Banco de Dados: utilize [Doctrine](https://www.doctrine-project.org/) para modelagem e interação com o banco de dados.
- Integração via API com a [Stripe](https://stripe.com/br).

## Páginas do E-commerce

O projeto deve contemplar duas páginas principais:

- **Catálogo de Produtos** - listagem com as informações de cada produto, incluindo: nome, descrição, imagem, preço, estoque, atributos e demais dados pertinentes.
- **Cadastro de Produtos** - formulário para cadastrar os produtos que serão exibidos no catálogo (página 1).

## Integração com a Stripe

- Crie sua conta na Stripe e obtenha as credenciais de acesso.
- Os produtos cadastrados no e-commerce deverão ser sincronizados também na Stripe.
- Na listagem de produtos, cada item deverá ter um botão "Comprar". Ao ser clicado, o produto é adicionado ao carrinho na Stripe, permitindo que o usuário prossiga com o processo de checkout diretamente pela Stripe.

## Funcionalidade Bônus (opcional, mas desejável)

Implemente um sistema de login/autenticação para proteger o acesso à página de cadastro de produtos, evitando que usuários não autorizados realizem inserções no catálogo.

## Entrega

Ao finalizar, disponibilize o projeto em um repositório público no GitHub ou GitLab, conforme sua preferência.

Prazo: até amanhã às 14h30.
Às 15h00, realizaremos uma call para apresentação da solução, validação da implementação e discussão dos próximos detalhes.