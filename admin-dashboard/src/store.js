import { create } from 'zustand'

export const useThemeStore = create((set) => ({
  isDark: localStorage.getItem('theme') === 'dark',
  toggleTheme: () => set((state) => {
    const newDarkMode = !state.isDark
    localStorage.setItem('theme', newDarkMode ? 'dark' : 'light')
    if (newDarkMode) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
    return { isDark: newDarkMode }
  }),
}))

export const useAuthStore = create((set) => ({
  user: {
    name: 'Admin User',
    email: 'admin@example.com',
    role: 'Administrator',
  },
  logout: () => {
    localStorage.removeItem('theme')
    window.location.href = '../Admin/admin_login.php'
  },
}))

export const useDashboardStore = create((set) => ({
  activePage: 'home',
  setActivePage: (page) => set({ activePage: page }),
  sidebarOpen: true,
  toggleSidebar: () => set((state) => ({ sidebarOpen: !state.sidebarOpen })),
}))
