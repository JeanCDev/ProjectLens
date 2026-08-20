import http from './http'
import type { PaginatedData, TeamMember } from './types'

export const TeamMemberService = {
  async list(projectId: number): Promise<TeamMember[]> {
    const { data } = await http.get<PaginatedData<TeamMember>>(`/projects/${projectId}/team-members`)
    return data.data
  },
}