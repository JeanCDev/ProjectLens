# ProjectLens — Relatório de Testes da API

**Data:** 18/08/2026
**Ambiente:** Laravel 13.26.0, PHP 8.3.16, SQLite, Windows (PowerShell + curl.exe)
**Método:** Testes manuais via HTTP (`curl.exe`) contra `php artisan serve` na porta 8000, autenticado com token Sanctum (`Authorization: Bearer`).

---

## 1. Resumo executivo

| Métrica | Resultado |
|---|---|
| Rotas testadas | **25 / 25** |
| Bugs encontrados | **1** (corrigido) |
| Status final | **Todas as rotas funcionando** |
| Lint (Pint) | passed |
| Testes automatizados | 2 passed |

---

## 2. Rota única de autenticação

| Teste | Método/URL | Resultado esperado | Resultado real | Status |
|---|---|---|---|---|
| Sem token | `GET /api/projects` | 401 | 401 | ✅ |
| Com token válido | `GET /api/projects` | 200 | 200 | ✅ |

---

## 3. CRUD — Projects

Base: `/api/projects`

| # | Rota | Método/URL | Resultado | Status |
|---|---|---|---|---|
| 1 | index | `GET /api/projects` | 200 | ✅ |
| 2 | store | `POST /api/projects` | 201 (id criado) | ✅ |
| 3 | show | `GET /api/projects/1` | 200 | ✅ |
| 4 | update | `PUT /api/projects/{id}` | 200 | ✅ |
| 5 | update | `PATCH /api/projects/{id}` | 200 | ✅ |
| 6 | destroy | `DELETE /api/projects/{id}` | 204 | ✅ |
| — | 404 | `DELETE /api/projects/999999` | 404 | ✅ |

**Filtros validados:**
- `GET /api/projects?status=active` → 200 (lista filtrada)
- `GET /api/projects?search=xyz` → 200 (busca por termo)

---

## 4. CRUD — Releases (aninhado em projects)

Base: `/api/projects/{project}/releases`

| # | Rota | Método/URL | Resultado | Status |
|---|---|---|---|---|
| 7 | index | `GET` | 200 | ✅ |
| 8 | store | `POST` | 201 (id criado) | ✅ |
| 9 | show | `GET /{release}` | 200 | ✅ |
| 10 | update | `PUT /{release}` | 200 | ✅ |
| 11 | destroy | `DELETE /{release}` | 204 | ✅ |
| — | 404 | `GET /api/projects/1/releases/1/releases` | 404 | ✅ |

---

## 5. CRUD — Environments (aninhado em projects)

Base: `/api/projects/{project}/environments`

| # | Rota | Método/URL | Resultado | Status |
|---|---|---|---|---|
| 12 | index | `GET` | 200 | ✅ |
| 13 | store | `POST` | 201 (id criado) | ✅ |
| 14 | show | `GET /{environment}` | 200 | ✅ |
| 15 | update | `PUT /{environment}` | 200 | ✅ |
| 16 | destroy | `DELETE /{environment}` | 204 | ✅ |

---

## 6. CRUD — Api Endpoints (aninhado em projects)

Base: `/api/projects/{project}/endpoints`

| # | Rota | Método/URL | Resultado inicial | Resultado final | Status |
|---|---|---|---|---|---|
| 17 | index | `GET` | 200 | — | ✅ |
| 18 | store | `POST` | 201 (id criado) | — | ✅ |
| 19 | show | `GET /{endpoint}` | **500** | 200 | ✅ |
| 20 | update | `PUT /{endpoint}` | **500** | 200 | ✅ |
| 21 | destroy | `DELETE /{endpoint}` | **500** | 204 | ✅ |

**Filtro validado:** `GET /api/projects/1/endpoints?method=GET` → 200

---

## 7. CRUD — Team Members (aninhado em projects)

Base: `/api/projects/{project}/team-members`

| # | Rota | Método/URL | Resultado | Status |
|---|---|---|---|---|
| 22 | index | `GET` | 200 | ✅ |
| 23 | store | `POST` | 201 (id criado) | ✅ |
| 24 | show | `GET /{team_member}` | 200 | ✅ |
| 25 | update | `PUT /{team_member}` | 200 | ✅ |
| 26 | destroy | `DELETE /{team_member}` | 204 | ✅ |

---

## 8. Validação (422)

| Teste | Envio | Resultado | Status |
|---|---|---|---|
| Enum inválido (project status) | `status: "inexistente"` | 422 com `errors.status` | ✅ |
| Enum inválido (role) | `role: "team_lead"` | 422 "The selected role is invalid." | ✅ |
| Role válido | `role: "manager"` → depois `GET` confirma `role: manager` | 200 | ✅ |

---

## 9. Bug encontrado e corrigido

**Sintoma:** `show`, `update` e `destroy` de endpoints retornavam HTTP 500.

**Causa raiz:** o parâmetro do método no controller era `ApiEndpoint $apiEndpoint`, mas a rota `apiResource('projects.endpoints')` gera o placeholder `{endpoint}`. O route model binding do Laravel casa o parâmetro do método com o nome do placeholder; como os nomes divergiam, o binding não resolvia e passava `null`.

**Log:**
```
TypeError: App\Services\ApiEndpointService::find(): Argument #2 ($id) must be of type int, null given
App\Services\ApiEndpointService::delete(): Return value must be of type bool, null returned
```

**Correção aplicada** em `app/Http/Controllers/Api/ApiEndpointController.php`:
- Renomeado o parâmetro `$apiEndpoint` → `$endpoint` nos métodos `show`, `update` e `destroy`.

**Confirmação:** após a correção, `GET` → 200, `PUT` → 200, `DELETE` → 204 para endpoints.

---

## 10. Validações finais

| Verificação | Comando | Resultado |
|---|---|---|
| Seed de banco limpo | `php artisan migrate:fresh --seed` | OK (10 projetos, 24 releases, 17 ambientes, 73 endpoints, 30 membros, 6 usuários) |
| Lint | `vendor/bin/pint` | passed |
| Testes | `php artisan test` | 2 passed |
| Rotas registradas | `php artisan route:list --path=api` | 25 rotas |

---

## 11. Conclusão

A API REST da ProjectLens foi validada integralmente. Todos os 25 endpoints respondem corretamente, incluindo CRUD completo dos 5 recursos, filtros, autenticação Sanctum (401/200) e validação de dados (422). Um bug de route model binding nos endpoints foi identificado por meio deste teste manual e corrigido.