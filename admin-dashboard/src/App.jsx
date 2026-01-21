import React, { useEffect } from 'react'
import Sidebar from './components/Sidebar'
import Header from './components/Header'
import DashboardHome from './components/DashboardHome'
import { useDashboardStore, useThemeStore } from './store'

function App() {
  const isDark = useThemeStore((state) => state.isDark)
  const activePage = useDashboardStore((state) => state.activePage)

  useEffect(() => {
    if (isDark) {
      document.documentElement.classList.add('dark')
    }
  }, [isDark])

  const renderContent = () => {
    switch (activePage) {
      case 'home':
        return <DashboardHome />
      case 'sessions':
        return <div className="p-6"><h2 className="text-2xl font-bold">Create Session</h2><p className="text-gray-500 mt-2">Session management coming soon...</p></div>
      case 'registrations':
        return <div className="p-6"><h2 className="text-2xl font-bold">View Registrations</h2><p className="text-gray-500 mt-2">Registration management coming soon...</p></div>
      case 'analytics':
        return <div className="p-6"><h2 className="text-2xl font-bold">Analytics & Reports</h2><p className="text-gray-500 mt-2">Analytics coming soon...</p></div>
      case 'modules':
        return <div className="p-6"><h2 className="text-2xl font-bold">Manage Modules</h2><p className="text-gray-500 mt-2">Module management coming soon...</p></div>
      case 'requests':
        return <div className="p-6"><h2 className="text-2xl font-bold">Form Requests</h2><p className="text-gray-500 mt-2">Request management coming soon...</p></div>
      case 'notifications':
        return <div className="p-6"><h2 className="text-2xl font-bold">Notifications</h2><p className="text-gray-500 mt-2">Notifications coming soon...</p></div>
      case 'settings':
        return <div className="p-6"><h2 className="text-2xl font-bold">Settings</h2><p className="text-gray-500 mt-2">Settings coming soon...</p></div>
      default:
        return <DashboardHome />
    }
  }

  return (
    <div className="flex h-screen bg-gray-50 dark:bg-slate-950">
      <Sidebar />
      <div className="flex-1 flex flex-col overflow-hidden">
        <Header />
        <main className="flex-1 overflow-auto">
          {renderContent()}
        </main>
      </div>
    </div>
  )
}

export default App
