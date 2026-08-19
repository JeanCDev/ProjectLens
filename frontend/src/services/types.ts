export type ProjectStatus = 'planning' | 'active' | 'on_hold' | 'completed' | 'archived'
export type ReleaseStatus = 'draft' | 'pre_release' | 'stable' | 'deprecated'
export type EnvironmentStatus = 'active' | 'inactive' | 'maintenance'
export type EnvironmentType = 'production' | 'staging' | 'development' | 'testing'
export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE' | 'OPTIONS' | 'HEAD'
export type EndpointStatus = 'healthy' | 'degraded' | 'down' | 'unknown'
export type TeamMemberRole = 'admin' | 'developer' | 'designer' | 'devops' | 'manager' | 'viewer'

export interface AuthUser {
  id: number
  name: string
  email: string
}

export interface Project {
  id: number
  name: string
  description: string
  primary_language: string
  framework: string | null
  status: ProjectStatus
  status_label?: string
  start_date?: string | null
  estimated_end_date?: string | null
  git_repository?: string | null
  documentation_url?: string | null
  created_at: string
  updated_at?: string
  environments?: Environment[]
  releases?: Release[]
  endpoints?: ApiEndpoint[]
  team_members?: TeamMember[]
}

export interface Release {
  id: number
  project_id: number
  version: string
  status: ReleaseStatus
  status_label?: string
  released_at?: string | null
  changelog: string[]
  created_at?: string
  updated_at?: string
}

export interface Environment {
  id: number
  project_id: number
  name: string
  type?: EnvironmentType
  url?: string
  database?: string
  version?: string
  status: EnvironmentStatus
  status_label?: string
  created_at?: string
  updated_at?: string
}

export interface ApiEndpoint {
  id: number
  project_id: number
  environment_id?: number
  name: string
  method: HttpMethod
  url: string
  status: EndpointStatus
  status_label?: string
  response_time_ms: number | null
  last_checked_at?: string | null
  environment?: Environment
  created_at?: string
  updated_at?: string
}

export interface TeamMember {
  id: number
  project_id: number
  user_id: number
  role: TeamMemberRole
  role_label?: string
  user?: {
    id: number
    name: string
    email: string
  }
  created_at?: string
  updated_at?: string
}

export interface ActivityItem {
  id: number
  actor: string
  action: string
  target: string
  timestamp: string
}

export interface AlertItem {
  id: number
  severity: 'critical' | 'warning' | 'info'
  title: string
  detail: string
  timestamp: string
}

export interface DashboardMetrics {
  totalProjects: number
  apisOnline: number
  activeReleases: number
  teamMembers: number
  uptime: number
  deploySeries: { name: string; data: number[] }[]
  apiSeries: { name: string; data: number[] }[]
  categories: string[]
}

export interface PaginatedData<T> {
  data: T[]
  links?: Record<string, string | null>
  meta?: {
    current_page: number
    from: number | null
    last_page: number
    per_page: number
    to: number | null
    total: number
  }
}