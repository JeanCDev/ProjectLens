export type ProjectStatus = 'planning' | 'active' | 'maintenance' | 'archived'
export type ReleaseStatus = 'planned' | 'in_progress' | 'released' | 'rolled_back'
export type EnvironmentStatus = 'active' | 'inactive'
export type EnvironmentType = 'production' | 'staging' | 'development' | 'testing'
export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'
export type EndpointStatus = 'active' | 'degraded' | 'down'
export type TeamMemberRole = 'owner' | 'admin' | 'developer' | 'viewer'

export interface Project {
  id: number
  name: string
  description: string
  framework: string
  language: string
  repository: string
  status: ProjectStatus
  owner: string
  last_deploy: string
  created_at: string
  endpoints_count: number
  releases_count: number
  environments_count: number
  members_count: number
}

export interface Release {
  id: number
  project_id: number
  version: string
  status: ReleaseStatus
  released_at: string
  changelog: string[]
  author: string
}

export interface Environment {
  id: number
  project_id: number
  name: string
  type: EnvironmentType
  url: string
  database: string
  version: string
  status: EnvironmentStatus
}

export interface ApiEndpoint {
  id: number
  project_id: number
  environment_id: number
  method: HttpMethod
  path: string
  description: string
  response_time_ms: number
  status: EndpointStatus
  last_checked_at: string
}

export interface TeamMember {
  id: number
  project_id: number
  name: string
  email: string
  role: TeamMemberRole
  joined_at: string
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