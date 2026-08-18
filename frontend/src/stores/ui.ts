import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const sidebarCollapsed = ref(false)
  const sidebarMobileOpen = ref(false)

  const isMobile = computed(() => typeof window !== 'undefined' && window.innerWidth < 1024)

  function toggleSidebar() {
    if (isMobile.value) {
      sidebarMobileOpen.value = !sidebarMobileOpen.value
    } else {
      sidebarCollapsed.value = !sidebarCollapsed.value
    }
  }

  function closeMobileSidebar() {
    sidebarMobileOpen.value = false
  }

  return { sidebarCollapsed, sidebarMobileOpen, isMobile, toggleSidebar, closeMobileSidebar }
})