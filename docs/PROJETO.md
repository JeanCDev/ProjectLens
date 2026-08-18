# ProjectLens

> Plataforma de gerenciamento técnico de projetos de software — API REST em Laravel + frontend Vue 3.

## Visão geral

ProjectLens é uma plataforma para organizar e acompanhar projetos de software. O **backend** expõe uma API REST e o **frontend** (Vue 3) é uma interface SaaS enterprise em dark mode. Cada projeto agrupa **releases**, **ambientes**, **endpoints** e **membros da equipe**, permitindo monitorar o ciclo técnico completo de um produto.

| Recurso | Descrição |
|---|---|
| **Projects** | Projetos de software (nome, linguagem, framework, status, datas) |
| **Releases** | Versões publicadas de cada projeto |
| **Environments** | Ambientes (produção, staging, dev...) de cada projeto |
| **Endpoints** | Endpoints de API monitorados, associados a um ambiente |
| **Team Members** | Membros da equipe de cada projeto |
| **Auth** | Cadastro de conta, login e logout via tokens Sanctum |
| **Frontend** | Interface Vue 3 + TypeScript com Dashboard, Projects, Endpoints, Releases, Environments e Settings |

## Stack

### Backend
| Camada | Tecnologia |
|---|---|
| Framework | Laravel 13.26.0 |
| Linguagem | PHP 8.3.16 (TS, x64) |
| Banco de dados | SQLite (`database/database.sqlite`) |
| Autenticação | Laravel Sanctum (tokens pessoais Bearer) |
| Documentação da API | Scramble (OpenAPI em `/docs/api`) |
| Testes | PHPUnit 12 + PCOV (cobertura 100%) |
| Code style | Laravel Pint |

### Frontend (`frontend/`)
| Camada | Tecnologia |
|---|---|
| Framework | Vue 3.5 (Composition API) + TypeScript |
| Build | Vite 6 (build direto para `../public/build`) |
| Estilização | TailwindCSS 4 + PrimeVue 5 (Aura, dark) |
| Estado | Pinia |
| Rotas | Vue Router |
| Ícones | Heroicons |
| Gráficos | ApexCharts (via vue3-apexcharts) |
| Utilitários | VueUse, Axios |
| Dados | Mocks via services com `async/await` + delay simulado |

## Como rodar

### Backend

```bash
# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Migrar e popular com dados de exemplo
php artisan migrate:fresh --seed

# Subir o servidor
php artisan serve
```

A aplicação sobe em `http://127.0.0.1:8000`.

### Frontend (desenvolvimento)

```bash
cd frontend
npm install
npm run dev
```

O Vite sobe em `http://localhost:5173` com **Hot Reload** e proxy para o backend (`/api` → `127.0.0.1:8000`).

### Produção

```bash
php artisan serve
cd frontend && npm run build
```

O build do Vite é enviado direto para `../public/build` (sem pasta `dist`) e o **Laravel serve todos os arquivos**. Não existe servidor Node em produção. O Blade `resources/views/app.blade.php` lê o manifest do Vite e injeta os assets automaticamente.

### Usuário seedado

| Email | Senha |
|---|---|
| `admin@projectlens.com` | `password` |

## Estrutura do projeto

```
app/
├── Http/
│   ├── Controllers/Api/     # Controllers dos 5 recursos + Auth
│   ├── Requests/            # FormRequests de validação (store/update + Auth)
│   └── Resources/           # Resources Eloquent de cada recurso
├── Models/                  # Project, Release, Environment, ApiEndpoint, TeamMember, User
├── Observers/               # ProjectObserver (gera token de análise)
├── Policies/                # Autorização por projeto (proprietário/membro)
├── Jobs/                    # AnalyzeProjectJob (análise em background)
└── Services/                # Lógica de negócio de cada recurso
database/
├── factories/               # Factories dos modelos
├── migrations/              # Migrations de todas as tabelas
└── seeders/                 # DatabaseSeeder + seeders específicos
routes/
├── api.php                  # 30 rotas da API
└── web.php                  # Rota catch-all que entrega o frontend (SPA)
resources/views/app.blade.php # Blade que injeta os assets do build do frontend
public/build/                # Build do frontend (gerado, ignorado no git)
tests/
├── Unit/                    # Enums, Models, Services, Policies, Resources, Requests, Jobs, Observers
└── Feature/Api/             # Testes de integração das rotas (CRUD + Auth)
frontend/
├── src/
│   ├── components/          # Componentes reutilizáveis (DataTable, StatusBadge, cards, charts...)
│   │   └── layout/          # Sidebar e Topbar
│   ├── layouts/             # DefaultLayout
│   ├── pages/               # Dashboard, Projects, ProjectDetails, Endpoints, Releases, Environments, Settings
│   ├── router/              # Configuração do Vue Router
│   ├── stores/              # Pinia (ui, dashboard, projects)
│   ├── services/            # Mocks HTTP (Project, Endpoint, Release, Environment, Dashboard)
│   ├── composables/         # Composables Vue
│   ├── utils/               # format.ts (datas/horas)
│   └── assets/              # CSS base + tema Tailwind
├── public/                  # favicon etc. (copiados para public/build)
├── package.json
└── vite.config.ts           # build → ../public/build, proxy /api → backend
docs/                        # Documentação deste projeto
```

## Frontend

### Arquitetura

- **Composition API** (`<script setup lang="ts">`) em todos os componentes.
- **State**: Pinia (`stores/`) — `ui` (sidebar), `dashboard` (métricas/alertas), `project` (projetos).
- **Dados**: todos os services em `frontend/src/services/` simulam chamadas HTTP com `Promise` + `async/await` + delay. Nenhum mock é colocado dentro de componentes.
- **Tema**: dark mode fixo, TailwindCSS 4 com paleta custom (`surface-*`) + PrimeVue 5 (preset Aura) para componentes interativos.

### Telas

| Rota | Tela | Conteúdo |
|---|---|---|
| `/` | Dashboard | 4 StatCards, gráficos de deploys/saúde, alertas, atividades, projetos recentes, releases |
| `/projects` | Projects | DataTable com busca, filtro por status, ordenação e paginação |
| `/projects/:id` | Project Details | Abas Overview, Endpoints, Releases, Environments, Members, Metrics |
| `/endpoints` | Endpoints | DataTable com método, URL, tempo de resposta, status, filtros |
| `/endpoints/:id` | Endpoint Details | Request (headers/body) e Response (status/headers/body) mockados |
| `/releases` | Releases | Timeline com versão, status, data e changelog |
| `/environments` | Environments | Cards por tipo (Production, Staging, Development) com URL, DB, version, status |
| `/settings` | Settings | Perfil, tema, notificações e API keys |

### Componentes reutilizáveis

`DataTable`, `StatusBadge`, `MethodBadge`, `MetricCard`, `StatCard`, `SectionCard`, `Loading`, `EmptyState`, `SearchInput`, `ConfirmDialog`, `PageHeader`, `Breadcrumb`, `AreaChart`, `StackedBarChart`.

## Autenticação

A API usa **tokens pessoais do Sanctum**. Dois endpoints públicos retornam o token:

### Criar conta — `POST /api/register`

```json
{
  "name": "Seu Nome",
  "email": "voce@email.com",
  "password": "senha-segura",
  "password_confirmation": "senha-segura"
}
```

Resposta `201`:
```json
{
  "token": "2|aBcD...eFgH",
  "token_type": "Bearer",
  "user": { "id": 2, "name": "Seu Nome", "email": "voce@email.com" }
}
```

### Login — `POST /api/login`

```json
{ "email": "admin@projectlens.com", "password": "password" }
```

Resposta `200` com o mesmo formato (token + user).

### Logout — `POST /api/logout`

Envie `Authorization: Bearer <token>`. Revoga o token atual; a partir daí ele retorna `401`.

> Todos os demais endpoints exigem o header `Authorization: Bearer <token>` e respondem `401` sem ele.

## Endpoints da API

### Auth (públicos)
| Método | Rota | Descrição |
|---|---|---|
| POST | `/api/register` | Criar conta e obter token |
| POST | `/api/login` | Login e obter token |

### Projects
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/projects` | Listar projetos (filtros: `?status=`, `?search=`) |
| POST | `/api/projects` | Criar projeto |
| GET | `/api/projects/{project}` | Exibir projeto |
| PUT/PATCH | `/api/projects/{project}` | Atualizar projeto |
| DELETE | `/api/projects/{project}` | Excluir projeto |

### Releases (por projeto)
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/projects/{project}/releases` | Listar releases |
| POST | `/api/projects/{project}/releases` | Criar release |
| GET | `/api/projects/{project}/releases/{release}` | Exibir release |
| PUT/PATCH | `/api/projects/{project}/releases/{release}` | Atualizar release |
| DELETE | `/api/projects/{project}/releases/{release}` | Excluir release |

### Environments (por projeto)
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/projects/{project}/environments` | Listar ambientes |
| POST | `/api/projects/{project}/environments` | Criar ambiente |
| GET | `/api/projects/{project}/environments/{environment}` | Exibir ambiente |
| PUT/PATCH | `/api/projects/{project}/environments/{environment}` | Atualizar ambiente |
| DELETE | `/api/projects/{project}/environments/{environment}` | Excluir ambiente |

### Endpoints de API (por projeto)
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/projects/{project}/endpoints` | Listar endpoints (filtros: `?method=`, `?search=`) |
| POST | `/api/projects/{project}/endpoints` | Criar endpoint |
| GET | `/api/projects/{project}/endpoints/{endpoint}` | Exibir endpoint |
| PUT/PATCH | `/api/projects/{project}/endpoints/{endpoint}` | Atualizar endpoint |
| DELETE | `/api/projects/{project}/endpoints/{endpoint}` | Excluir endpoint |

### Team Members (por projeto)
| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/projects/{project}/team-members` | Listar membros |
| POST | `/api/projects/{project}/team-members` | Adicionar membro |
| GET | `/api/projects/{project}/team-members/{teamMember}` | Exibir membro |
| PUT/PATCH | `/api/projects/{project}/team-members/{teamMember}` | Atualizar membro |
| DELETE | `/api/projects/{project}/team-members/{teamMember}` | Remover membro |

## Enums

| Enum | Valores |
|---|---|
| `ProjectStatus` | `planning`, `active`, `maintenance`, `archived` |
| `ReleaseStatus` | `planned`, `in_progress`, `released`, `rolled_back` |
| `EnvironmentStatus` | `active`, `inactive` |
| `EnvironmentType` | `production`, `staging`, `development`, `testing` |
| `HttpMethod` | `GET`, `POST`, `PUT`, `PATCH`, `DELETE` |
| `ApiEndpointStatus` | `active`, `degraded`, `down` |
| `TeamMemberRole` | `owner`, `admin`, `developer`, `viewer` |

## Documentação interativa

Com o servidor rodando em ambiente local:

- **UI (Stoplight Elements):** `http://127.0.0.1:8000/docs/api`
- **OpenAPI JSON:** `http://127.0.0.1:8000/docs/api.json`

A documentação é gerada pelo **Scramble** diretamente do código (controllers, FormRequests e Resources). Em produção ela fica bloqueada (`RestrictedDocsAccess`).

## Testes

```bash
# Rodar a suíte completa
php artisan test

# Rodar com relatório de cobertura (PCOV)
php artisan test --coverage
```

Estado atual: **167 testes / 464 assertions passando**, **100% de cobertura** em todos os arquivos de `app/`.

| Grupo | O que cobre |
|---|---|
| `tests/Unit/Enums` | Valores, labels e casts de todos os enums |
| `tests/Unit/Models` | Relationships, casts, fillable e mutators dos modelos |
| `tests/Unit/Services` | Lógica de negócio dos serviços |
| `tests/Unit/Policies` | Regras de autorização por papel |
| `tests/Unit/Resources` | Formato de resposta de cada resource |
| `tests/Unit/Requests` | Regras de validação dos FormRequests |
| `tests/Unit/Jobs` | AnalyzeProjectJob |
| `tests/Unit/Observers` | ProjectObserver |
| `tests/Feature/Api` | CRUD das 25 rotas + fluxo completo de auth (register/login/logout) |

### Code style

```bash
vendor/bin/pint        # corrige automaticamente
vendor/bin/pint --test # apenas verifica
```

## Notas de desenvolvimento

- **Bug corrigido:** o `ApiEndpointController` usava o parâmetro `$apiEndpoint` enquanto a rota gera `{endpoint}` — o route model binding falhava (500 em show/update/destroy). Renomeado para `$endpoint`.
- **Pitfall PHPUnit 12:** não descobre múltiplas classes de teste num único arquivo — 1 classe por arquivo.
- **Pitfall Sanctum em testes:** o guard fica em cache no container entre requisições do mesmo teste — usar `$this->app->make('auth')->forgetGuards()` (e não `refreshApplication()`, que perde o SQLite `:memory:`).
- **PCOV:** driver de cobertura instalado manualmente (TS x64) — DLL em `C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\ext\php_pcov.dll`, habilitado no `php.ini`.
- **Constraints únicas:** `team_members(project_id, user_id)` — duplicidade gera 500 (comportamento esperado).

## Documentos relacionados

- `GUIA_DOCS_API.md` — como usar a documentação do Scramble, autenticar e testar todos os endpoints pela tela
- `RELATORIO_TESTES.md` — relatório dos testes manuais (curl) das 25 rotas
- `CHANGELOG.md` — histórico de alterações
- `README.md` — apresentação do repositório