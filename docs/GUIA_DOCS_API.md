# ProjectLens — Guia de Uso da Documentação da API

Este documento explica como acessar a documentação, autenticar e testar **todos os endpoints** pela interface gerada pelo Scramble (`/docs/api`).

---

## 1. O que é o ProjectLens API

API REST para gerenciamento técnico de projetos de software. Organizada em 5 recursos:

| Recurso | Descrição |
|---|---|
| **Projects** | Projetos de software (nome, linguagem, framework, status, datas) |
| **Releases** | Versões publicadas de cada projeto |
| **Environments** | Ambientes (produção, staging, dev...) de cada projeto |
| **Endpoints** | Endpoints de API monitorados, associados a um ambiente |
| **Team Members** | Membros da equipe de cada projeto |

Todos os endpoints são **protegidos** por autenticação via **Sanctum** (token Bearer).

---

## 2. Subindo a aplicação

Na raiz do projeto, suba o servidor de desenvolvimento:

```
php artisan serve
```

Depois abra no navegador:

| URL | O que é |
|---|---|
| `http://127.0.0.1:8000/docs/api` | Interface interativa da documentação |
| `http://127.0.0.1:8000/docs/api.json` | Especificação OpenAPI em JSON |

> A documentação **só é exibida em ambiente local**. Em produção ela fica bloqueada (controle via `RestrictedDocsAccess`).

---

## 3. Como fazer login

A autenticação usa **tokens pessoais do Sanctum** (`Authorization: Bearer <token>`). Existem dois endpoints públicos que retornam o token automaticamente:

```
POST /api/register
POST /api/login
```

### Criar conta

```
POST /api/register
```

Body (JSON):
```json
{
  "name": "Seu Nome",
  "email": "voce@email.com",
  "password": "senha-segura",
  "password_confirmation": "senha-segura"
}
```

Resposta (201):
```json
{
  "token": "2|aBcD...eFgH",
  "token_type": "Bearer",
  "user": { "id": 2, "name": "Seu Nome", "email": "voce@email.com" }
}
```

Regras: nome obrigatório (máx. 255), e-mail obrigatório e único, senha com **mínimo de 8 caracteres** e campo `password_confirmation` idêntico. E-mail duplicado retorna **422**.

### Entrar (login)

```
POST /api/login
```

Body (JSON):
```json
{
  "email": "admin@projectlens.com",
  "password": "password"
}
```

Resposta (200):
```json
{
  "token": "1|aBcD...eFgH",
  "token_type": "Bearer",
  "user": { "id": 1, "name": "Admin", "email": "admin@projectlens.com" }
}
```

O token retornado vale para **todas as demais rotas protegidas**. Para encerrar a sessão:

```
POST /api/logout          (enviando Authorization: Bearer <token>)
```

### 3.1 Credenciais de acesso

Usuários são criados pelos seeders. Após `php artisan migrate:fresh --seed`:

| Email | Senha |
|---|---|
| `admin@projectlens.com` | `password` |

### 3.2 Usando o token na documentação

Na tela de `/docs/api` (Stoplight Elements):

1. Clique no botão **Authorize** (canto superior direito, ícone de cadeado).
2. Em **HTTP Bearer**, cole apenas a parte **após o `|`** do token: `aBcD...eFgH`.
3. Clique em **Authorize**.

Agora todas as requisições "Try It" vão enviar o header `Authorization: Bearer <token>` automaticamente.

> **Formato correto do token:** o valor completo retornado é `1|aBcD...eFgH`. Na interface, **não** inclua o prefixo `1|` — envie só o hash (`aBcD...eFgH`).
>
> **Alternativa sem copiar/colar:** teste `POST /api/register` (crie sua conta) ou `POST /api/login` pelo "Try It" da própria documentação — eles são públicos (não precisam de Authorize) e retornam o `token` na resposta. Depois copie o token (sem o prefixo `1|`) para o Authorize.

### 3.3 Testando sem a interface (curl)

```bash
# 1. Criar conta (retorna o token)
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name":"Seu Nome","email":"voce@email.com","password":"senha-segura","password_confirmation":"senha-segura"}'

# 2. Login (retorna o token)
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"voce@email.com","password":"senha-segura"}'

# 3. Usar o token nas rotas protegidas
curl -X GET http://127.0.0.1:8000/api/projects \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

---

## 4. Como usar a tela "Try It"

Cada endpoint da documentação tem um botão **Try It**:

1. Clique em **Try It** no endpoint desejado.
2. Preencha os **parâmetros** (path, query e body) — o Scramble mostra os campos com tipos, campos obrigatórios e valores válidos (ex.: enums).
3. Clique em **Send**.
4. Veja a **resposta** (status, headers e corpo JSON).

Respostas esperadas:
- **200** — leitura/atualização com sucesso
- **201** — criação com sucesso
- **204** — exclusão com sucesso (sem corpo)
- **401** — token ausente/ inválido
- **404** — recurso não encontrado (ou não pertence ao projeto)
- **422** — validação falhou (campos inválidos ou obrigatórios faltando)

---

## 5. Endpoints — Auth (login/logout)

### POST `/api/register` — Criar conta e obter token (público)
- **Não exige** token — é o único endpoint público junto com o login.
- Body (JSON): `name` (obrigatório, máx. 255), `email` (obrigatório, formato e-mail, único), `password` (obrigatório, **mín. 8**) e `password_confirmation` (idêntico à senha).
- **Respostas:**
  - **201** — `{ "token": "2|...", "token_type": "Bearer", "user": {...} }`
  - **422** — campos inválidos ou e-mail já cadastrado (mensagem em PT-BR)
- **Uso na tela de docs:** clique em **Try It**, preencha os campos, clique **Send** e copie o `token` da resposta.

### POST `/api/login` — Autenticar e obter token (público)
- **Não exige** token — é o único endpoint público da API.
- Body (JSON): `email` (obrigatório, formato e-mail) e `password` (obrigatório).
- **Respostas:**
  - **200** — `{ "token": "1|...", "token_type": "Bearer", "user": {...} }`
  - **422** — credenciais incorretas ou campos inválidos (mensagem em PT-BR)
- **Uso na tela de docs:** clique em **Try It**, preencha email/senha, clique **Send** e copie o `token` da resposta.

### POST `/api/logout` — Encerrar sessão (protegido)
- **Exige** `Authorization: Bearer <token>` (Authorize).
- **Resposta:** 200 — `{ "message": "Logout realizado com sucesso." }`
- Revoga o token atual; ele passa a retornar **401** nas rotas protegidas.

---

## 6. Endpoints — Projects

Base: `/api/projects`

### GET `/api/projects` — Listar projetos
- **Query opcional:** `status` (`planning`, `active`, `on_hold`, `completed`, `archived`), `search` (busca em nome/descrição), `language` (ex.: `PHP`)
- **Exemplo:** `GET /api/projects?status=active&search=api&language=PHP`
- **Resposta:** 200 — lista paginada (15 por página) com relacionamentos `environments`, `releases`, `endpoints`, `team_members`

### POST `/api/projects` — Criar projeto
Body (JSON):
```json
{
  "name": "Portal de Pagamentos",
  "description": "Sistema de cobranças e assinaturas",
  "primary_language": "PHP",
  "framework": "Laravel",
  "status": "planning",
  "start_date": "2026-08-01",
  "estimated_end_date": "2027-02-01",
  "git_repository": "https://github.com/empresa/pagamentos",
  "documentation_url": "https://docs.empresa.com/pagamentos"
}
```
- `name` é **obrigatório** (máx. 255). Demais campos opcionais.
- `status` aceita apenas: `planning`, `active`, `on_hold`, `completed`, `archived`.
- **Resposta:** 201 — projeto criado (com `status_label` em PT-BR)

### GET `/api/projects/{project}` — Ver projeto
- `{project}` = id do projeto (ex.: `1`)
- **Resposta:** 200 — projeto com todos os relacionamentos carregados

### PUT `/api/projects/{project}` — Atualizar projeto (completo)
- Envie os campos que quiser alterar; aceita atualização parcial também
- **Resposta:** 200 — projeto atualizado

### PATCH `/api/projects/{project}` — Atualização parcial
- Mesmo comportamento do PUT
- **Resposta:** 200

### DELETE `/api/projects/{project}` — Excluir projeto
- **Resposta:** 204 — sem corpo

---

## 6. Endpoints — Releases (por projeto)

Base: `/api/projects/{project}/releases`

### GET `/api/projects/{project}/releases` — Listar releases
- `{project}` = id do projeto
- **Query opcional:** `status` (`draft`, `pre_release`, `stable`, `deprecated`)
- **Resposta:** 200 — lista paginada

### POST `/api/projects/{project}/releases` — Criar release
Body (JSON):
```json
{
  "version": "1.0.0",
  "changelog": "Primeira versão com autenticação e relatórios.",
  "released_at": "2026-08-18",
  "status": "stable"
}
```
- `version` é **obrigatório** (máx. 50).
- **Resposta:** 201

### GET `/api/projects/{project}/releases/{release}` — Ver release
- **Resposta:** 200
- **404** se o release não pertence ao projeto informado

### PUT `/api/projects/{project}/releases/{release}` — Atualizar release
- **Resposta:** 200

### DELETE `/api/projects/{project}/releases/{release}` — Excluir release
- **Resposta:** 204

---

## 7. Endpoints — Environments (por projeto)

Base: `/api/projects/{project}/environments`

### GET `/api/projects/{project}/environments` — Listar ambientes
- **Resposta:** 200 — lista paginada

### POST `/api/projects/{project}/environments` — Criar ambiente
Body (JSON):
```json
{
  "name": "Production",
  "url": "https://app.empresa.com",
  "database": "MySQL 8.0",
  "version": "2.1.0",
  "status": "active"
}
```
- `name` é **obrigatório** (máx. 255).
- `status` aceita: `active`, `inactive`, `maintenance`.
- **Resposta:** 201

### GET `/api/projects/{project}/environments/{environment}` — Ver ambiente
- **Resposta:** 200
- **404** se o ambiente não pertence ao projeto informado

### PUT `/api/projects/{project}/environments/{environment}` — Atualizar ambiente
- **Resposta:** 200

### DELETE `/api/projects/{project}/environments/{environment}` — Excluir ambiente
- **Resposta:** 204

---

## 8. Endpoints — Endpoints de API (por projeto)

Base: `/api/projects/{project}/endpoints`

> **Atenção:** o recurso usa o placeholder `{endpoint}` na URL. Na interface, o campo é `endpoint` (não `apiEndpoint`).

### GET `/api/projects/{project}/endpoints` — Listar endpoints
- **Query opcional:** `status` (`healthy`, `degraded`, `down`, `unknown`), `method` (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`, `HEAD`)
- **Exemplo:** `GET /api/projects/1/endpoints?status=healthy&method=GET`
- **Resposta:** 200 — lista paginada, cada item com o objeto `environment` embutido

### POST `/api/projects/{project}/endpoints` — Criar endpoint
Body (JSON):
```json
{
  "environment_id": 1,
  "name": "Health Check",
  "method": "GET",
  "url": "/api/health",
  "status": "healthy"
}
```
- **Obrigatórios:** `environment_id` (deve existir — use um id de ambiente do projeto), `name`, `method`, `url`.
- `method` aceita apenas: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`, `HEAD`.
- `status` aceita: `healthy`, `degraded`, `down`, `unknown`.
- **Resposta:** 201

### GET `/api/projects/{project}/endpoints/{endpoint}` — Ver endpoint
- **Resposta:** 200 — inclui `environment` embutido
- **404** se o endpoint não pertence ao projeto informado

### PUT `/api/projects/{project}/endpoints/{endpoint}` — Atualizar endpoint
- **Resposta:** 200

### DELETE `/api/projects/{project}/endpoints/{endpoint}` — Excluir endpoint
- **Resposta:** 204

---

## 9. Endpoints — Team Members (por projeto)

Base: `/api/projects/{project}/team-members`

### GET `/api/projects/{project}/team-members` — Listar membros
- **Resposta:** 200 — lista paginada, cada item com o objeto `user` embutido (`id`, `name`, `email`)

### POST `/api/projects/{project}/team-members` — Adicionar membro
Body (JSON):
```json
{
  "user_id": 2,
  "role": "developer"
}
```
- `user_id` é **obrigatório** e deve existir na tabela `users`.
- `role` aceita: `admin`, `developer`, `designer`, `devops`, `manager`, `viewer`.
- **Importante:** a combinação `(project_id, user_id)` é **única** — não dá para adicionar o mesmo usuário duas vezes ao mesmo projeto (erro 500).
- **Resposta:** 201 — com `user` embutido

### GET `/api/projects/{project}/team-members/{team_member}` — Ver membro
- Na interface, o placeholder aparece como `teamMember`.
- **Resposta:** 200 — com `user` embutido

### PUT `/api/projects/{project}/team-members/{team_member}` — Atualizar membro
- Exemplo de body: `{ "role": "manager" }`
- **Resposta:** 200 — com `user` embutido

### DELETE `/api/projects/{project}/team-members/{team_member}` — Remover membro
- **Resposta:** 204

---

## 11. Fluxo sugerido para um primeiro teste completo

1. **Crie uma conta** — teste `POST /api/register` na própria tela de docs (seção 5) e copie o `token` da resposta. **Autorize** com esse token (seção 3.2).
2. `POST /api/projects` — crie um projeto (ex.: "Portal de Pagamentos"). Anote o `id`.
3. `GET /api/projects/{id}` — confirme o projeto criado.
4. `POST /api/projects/{id}/environments` — crie o ambiente "Production".
5. `POST /api/projects/{id}/endpoints` — crie um endpoint usando o `environment_id` do passo 4.
6. `POST /api/projects/{id}/releases` — crie a release `1.0.0`.
7. `GET /api/projects/{id}/team-members` — veja os membros seedados.
8. `PUT /api/projects/{id}` — mude o `status` para `active` e veja o `status_label` mudar para "Ativo".
9. `DELETE /api/projects/{id}/endpoints/{endpoint}` e `DELETE /api/projects/{id}` — limpe o que criou.
10. `POST /api/logout` — encerre a sessão e confirme que o token passou a dar **401**.

---

## 12. Erros comuns e soluções

| Problema | Causa | Solução |
|---|---|---|
| `401 Unauthenticated` em tudo | Token não autorizado na tela | Clique em **Authorize** e cole o token (sem o prefixo `1\|`) |
| `422` no `POST /api/login` | Credenciais erradas ou campo faltando | Confira email/senha (padrão: `admin@projectlens.com` / `password`) e envie ambos os campos |
| `422` no `POST /api/register` | E-mail já cadastrado, senha curta ou confirmação diferente | Use outro e-mail, senha com 8+ caracteres e `password_confirmation` idêntico |
| `404` ao acessar recurso aninhado | O id informado não pertence ao projeto da URL | Use um id que exista naquele projeto (ex.: `GET /api/projects/1/releases` para descobrir) |
| `422` com "The selected role/status/method is invalid" | Valor fora do enum | Use um dos valores listados na documentação (o Scramble mostra o dropdown) |
| `422` com "field is required" | Campo obrigatório vazio | Preencha `name`, `version`, `user_id`, `environment_id` etc. conforme a seção do recurso |
| `500` ao criar Team Member | Usuário já é membro do projeto (constraint única) | Use outro `user_id` ou outro projeto |
| `404` na tela `/docs/api` | Ambiente não é `local` | Suba o servidor em ambiente local (`php artisan serve`) |

---

## 13. Referência rápida dos enums

| Campo | Valores aceitos |
|---|---|
| Project `status` | `planning`, `active`, `on_hold`, `completed`, `archived` |
| Release `status` | `draft`, `pre_release`, `stable`, `deprecated` |
| Environment `status` | `active`, `inactive`, `maintenance` |
| Endpoint `status` | `healthy`, `degraded`, `down`, `unknown` |
| Endpoint `method` | `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`, `HEAD` |
| Team Member `role` | `admin`, `developer`, `designer`, `devops`, `manager`, `viewer` |

---

## 14. Testes automatizados

A suíte tem **151 testes** cobrindo **100%** do código da aplicação:

```
php artisan test              # roda os testes
php artisan test --coverage   # testes + relatório de cobertura
```