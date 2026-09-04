# Gestao de Gastos

Aplicacao em desenvolvimento para controle de gastos pessoais/financeiros, usando Laravel, Inertia, Vue 3, Vite e Vuetify.

## Estado atual

O projeto ja possui a base tecnica instalada:

- Laravel com Inertia configurado.
- Vue 3 com Vuetify configurado.
- Vite configurado para build dos assets.
- Banco configurado via `.env`.
- Migrations padrao do Laravel executadas.
- Autenticacao basica implementada.
- Tela inicial apontando para a pagina de login.
- Criacao de usuarios protegida por login.
- Login valida se o usuario existe, se esta ativo e se a senha confere.
- Usuario administrador padrao criado automaticamente por migration.
- Modulo de usuarios finalizado com listagem, criacao, edicao, desativacao e troca de senha.
- Dominio financeiro inicial implementado com categorias e lancamentos.
- Categorias definem se o lancamento e uma receita ou despesa.
- Lancamentos possuem data, categoria, valor e descricao opcional.
- Tema visual claro implementado no padrao de painel administrativo.
- Dashboard com cards totalizadores do mes atual, ultimos 3 meses e ano atual.
- Dashboard agrupa receitas e despesas por categoria dentro de cada periodo.
- Tela de lancamentos com formulario de cadastro e tabela com dados reais.
- Lancamentos podem ser editados e excluidos.
- Layout responsivo refinado para celular e tablet.
- Estados vazios, carregamento e mensagens de erro foram adicionados nas telas principais do fluxo de lancamentos.
- Filtros de lancamentos por periodo, categoria e tipo foram implementados.
- Estados vazios e mensagens de erro foram evoluidos em categorias, usuarios e lancamentos.
- Tela de relatorios criada com filtros, resumo, agrupamento por categoria e evolucao mensal.

Validacoes feitas:

- `php artisan route:list` funciona.
- `php artisan test` passou.
- `npm.cmd run build` gerou o build de producao.
- A aplicacao respondeu HTTP 200 em `http://127.0.0.1:8000`.

Usuario inicial:

```txt
E-mail: admin@turingdesenvolvimento.com
Senha: admin@123
```

Esse usuario e criado pela migration `database/migrations/2026_08_05_000000_create_default_admin_user.php`.

## Rotas implementadas

- `GET /` - Login
- `GET /login` - Login
- `POST /login` - Autenticar usuario
- `GET /dashboard` - Dashboard protegido
- `GET /lancamentos` - Tela de lancamentos
- `GET /lancamentos?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD&category_id=ID&type=receita|despesa` - Filtrar lancamentos
- `POST /lancamentos` - Criar lancamento financeiro
- `GET /lancamentos/{entry}/editar` - Editar lancamento financeiro
- `PUT /lancamentos/{entry}` - Atualizar lancamento financeiro
- `DELETE /lancamentos/{entry}` - Excluir lancamento financeiro
- `GET /categorias` - Listagem e cadastro de categorias
- `POST /categorias` - Criar categoria
- `PUT /categorias/{category}` - Atualizar categoria
- `GET /relatorios` - Tela de relatorios
- `GET /relatorios?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD&category_id=ID&type=receita|despesa` - Filtrar relatorios
- `GET /usuarios` - Listagem de usuarios
- `GET /usuarios/novo` - Criar usuario, apenas logado
- `POST /usuarios` - Salvar usuario, apenas logado
- `GET /usuarios/{user}/editar` - Editar usuario
- `PUT /usuarios/{user}` - Atualizar usuario
- `DELETE /usuarios/{user}` - Desativar usuario
- `GET /minha-senha` - Tela de alteracao da propria senha
- `PUT /minha-senha` - Atualizar propria senha
- `POST /logout` - Encerrar sessao

## Tema visual

O tema atual segue um padrao de dashboard claro:

- Sidebar fixa em fundo branco.
- Azul como cor principal.
- Cards brancos com borda suave e sombra discreta.
- Topo com notificacao e dados do usuario.
- Dashboard com cards totalizadores.
- Dashboard com agrupamento por categoria em cada periodo.
- Tela de relatorios com cards de resumo e tabelas analiticas.
- Tela de lancamentos com formulario de novo lancamento.
- Tela de lancamentos com filtros por periodo, categoria e tipo.
- Tabela de lancamentos com etiquetas coloridas por tipo.
- Layout adaptado para celular e tablet, com menu superior compacto e navegacao horizontal.
- Tela de categorias para definir receitas e despesas.
- Tela de login ajustada para a mesma identidade visual.

Arquivos principais do tema:

- `resources/js/Layouts/AppLayout.vue`
- `resources/js/Layouts/Guest.vue`
- `resources/js/Pages/Dashboard.vue`
- `resources/js/Pages/Reports/Index.vue`
- `resources/js/Pages/Authentication/Login.vue`
- `resources/js/vuetify.js`

## O que falta fazer

### 1. Autenticacao

- Rotas reais de login, logout e criacao de usuario foram criadas.
- `AuthController` foi implementado.
- `UserController` foi implementado.
- Formulario de login foi corrigido.
- Dependencias inexistentes do formulario foram removidas.
- Redirecionamento apos login foi definido para `/dashboard`.
- Criacao de usuario so funciona para usuario autenticado.
- Usuarios possuem campo `active`.
- Usuarios inativos nao conseguem acessar o sistema.
- Nao existe regra de permissao por perfil: todo usuario ativo acessa os mesmos dados.
- Tela para alterar senha do usuario logado foi criada.
- Listagem de usuarios foi criada.
- Edicao de usuarios foi criada.
- Desativacao de usuarios foi criada.
- O sistema impede desativar o proprio usuario.

Antes de colocar em producao:

- Entrar com o usuario administrador padrao.
- Acessar `Minha senha`.
- Trocar a senha `admin@123` por uma senha forte.

### 2. Dominio de gestao de gastos

- Migrations principais foram criadas.
- Models Eloquent foram criados.
- Controllers e rotas foram criados.
- Validacoes de entrada foram implementadas.
- Os dados financeiros serao compartilhados entre todos os usuarios ativos.
- Categorias possuem `name`, `type` e `active`.
- O tipo da categoria pode ser `receita` ou `despesa`.
- Lancamentos possuem `entry_date`, `category_id`, `amount` e `description`.
- A descricao do lancamento e opcional.
- A data do lancamento vem preenchida por padrao com a data atual.
- Categorias inativas nao podem ser usadas em novos lancamentos.
- Lancamentos podem ser editados.
- Lancamentos podem ser excluidos.
- Lancamentos podem ser filtrados por data inicial, data final, categoria e tipo.
- Relatorios podem ser filtrados por data inicial, data final, categoria e tipo.
- Dashboard calcula receitas, despesas e saldo do mes atual.
- Dashboard calcula receitas, despesas e saldo dos ultimos 3 meses.
- Dashboard calcula receitas, despesas e saldo do ano atual.
- Dashboard agrupa gastos e receitas por categoria em cada periodo.
- Relatorios calculam receitas, despesas, saldo e quantidade de lancamentos no periodo.
- Relatorios agrupam receitas e despesas por categoria.
- Relatorios agrupam receitas, despesas e saldo por mes.

Entidades sugeridas:

- Categorias, implementado.
- Lancamentos financeiros, implementado.
- Contas ou carteiras, pendente se necessario.
- Cartoes, pendente se necessario.
- Lancamentos recorrentes, pendente se necessario.
- Metas ou orcamentos mensais, pendente se necessario.

### 3. Telas principais

- Dashboard financeiro foi criado com totalizadores do mes, ultimos 3 meses e ano.
- Cadastro de lancamentos foi criado.
- Listagem de lancamentos foi criada.
- Edicao de lancamentos foi criada.
- Exclusao de lancamentos foi criada.
- Gestao de categorias foi criada.
- Filtros por periodo, categoria e tipo foram criados.
- Resumo mensal, ultimos 3 meses e anual foi criado no dashboard.
- Tela de relatorios foi criada.
- Relatorios por categoria foram criados.
- Relatorios por mes foram criados.

Pendencias da interface:

- Refinar telas adicionais conforme novas funcionalidades forem criadas.

### 4. Regras de negocio

- Calcular saldo do periodo, implementado no dashboard.
- Separar receitas e despesas, implementado via tipo da categoria.
- Agrupar gastos e receitas por categoria, implementado no dashboard e em relatorios.
- Exibir totais mensais, ultimos 3 meses e anuais.
- Permitir filtro por data, categoria e tipo em lancamentos e relatorios.
- Definir comportamento para lancamentos recorrentes.
- Definir se despesas podem ter status, como pago/pendente.

### 5. Banco de dados

- Confirmar se o projeto usara MySQL ou SQLite.
- Atualizar `.env.example` com a configuracao correta.
- Criar seeders para dados iniciais, se necessario.
- Criar factories para testes.

Atualmente existe `database/database.sqlite`, mas o `.env` esta apontando para MySQL:

```env
DB_CONNECTION=mysql
DB_DATABASE=gestao-gastos
```

### 6. Qualidade e testes

- Testes para autenticacao foram criados.
- Testes para gestao de usuarios foram criados.
- Testes para categorias e lancamentos foram criados.
- Testes para edicao e exclusao de lancamentos foram criados.
- Testes para filtros de lancamentos foram criados.
- Testes para relatorios e totais financeiros foram criados.
- Adicionar validacao de frontend, se for manter formularios ricos no Vue.

### 7. Ajustes de ambiente

- Corrigir o aviso de Xdebug no PHP.
- Usar `npm.cmd` no PowerShell caso `npm` esteja bloqueado pela execution policy.
- Revisar `APP_NAME`, `APP_URL`, idioma e timezone no `.env`.

## Comandos uteis

Instalar dependencias PHP:

```bash
composer install
```

Instalar dependencias JavaScript:

```bash
npm install
```

Executar migrations:

```bash
php artisan migrate
```

Rodar servidor Laravel:

```bash
php artisan serve
```

Rodar Vite em desenvolvimento:

```bash
npm.cmd run dev
```

Gerar build de producao:

```bash
npm.cmd run build
```

Rodar testes:

```bash
php artisan test
```

## Observacoes

O projeto ainda nao possui funcionalidades completas de gestao de gastos. A base tecnica esta montada, a autenticacao inicial ja funciona e o tema visual do dashboard foi iniciado, mas ainda falta implementar as entidades financeiras, persistencia dos lancamentos, regras de negocio e testes do dominio.

O PHP local ainda exibe um aviso de Xdebug mal configurado ao rodar comandos Artisan. Esse aviso nao impediu migrations, testes ou build, mas deve ser corrigido no ambiente.
