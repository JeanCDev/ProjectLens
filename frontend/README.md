# ProjectLens Frontend

Interface Vue 3 + TypeScript do ProjectLens — SaaS enterprise dark mode para gerenciamento técnico de projetos.

## Stack

- Vue 3.5 (Composition API, `<script setup>`)
- TypeScript
- Vite 6
- TailwindCSS 4 + PrimeVue 5 (Aura, dark)
- Pinia, Vue Router, VueUse, Axios
- Heroicons, ApexCharts

## Requisitos

- Node.js ≥ 22
- Backend Laravel rodando em `http://127.0.0.1:8000`

## Desenvolvimento

```bash
npm install
npm run dev
```

Sobe em `http://localhost:5173` com **Hot Reload**. Requisições para `/api/*` são proxyadas para o backend (`http://127.0.0.1:8000`).

## Produção

```bash
npm run build
```

O build é enviado diretamente para `../public/build` (não gera `dist`). O Laravel serve todos os arquivos estáticos e o Blade `resources/views/app.blade.php` injeta os assets lendo o manifest do Vite. **Não existe servidor Node em produção.**

## Comandos

| Comando | Descrição |
|---|---|
| `npm run dev` | Servidor de desenvolvimento com HMR |
| `npm run build` | Build de produção para `../public/build` |
| `npm run type-check` | Verificação de tipos (vue-tsc) |
| `npm run preview` | Pré-visualizar o build |

## Estrutura

```
src/
├── components/     # Reutilizáveis (DataTable, StatusBadge, StatCard, charts...)
│   └── layout/     # Sidebar, Topbar
├── layouts/        # DefaultLayout
├── pages/          # Dashboard, Projects, ProjectDetails, Endpoints, Releases, Environments, Settings
├── router/         # Vue Router
├── stores/         # Pinia
├── services/       # Mocks HTTP (Project, Endpoint, Release, Environment, Dashboard)
├── composables/    # Composables
├── utils/          # format.ts
└── assets/         # CSS e tema
```

## Dados

Todos os dados são **mocks** servidos por `src/services/*`. Cada método retorna `Promise` com `async/await` e um pequeno delay simulando uma API real. Para conectar ao backend real, basta trocar a implementação dos services pelas chamadas Axios aos endpoints de `/api`.