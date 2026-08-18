import { defineStore } from 'pinia'
import { ref } from 'vue'
import { ProjectService } from '@/services/ProjectService'
import type { Project } from '@/services/types'

export const useProjectStore = defineStore('project', () => {
  const projects = ref<Project[]>([])
  const current = ref<Project | null>(null)
  const loading = ref(false)

  async function fetchProjects() {
    loading.value = true
    try {
      const { items } = await ProjectService.list()
      projects.value = items
    } finally {
      loading.value = false
    }
  }

  async function fetchProject(id: number) {
    loading.value = true
    try {
      current.value = await ProjectService.get(id)
    } finally {
      loading.value = false
    }
  }

  return { projects, current, loading, fetchProjects, fetchProject }
})