import React from 'react'
import { X } from 'lucide-react'
import { useDashboardStore } from '../store'
import { menuItems } from '../data/mockData'

export default function Sidebar() {
  const activePage = useDashboardStore((state) => state.activePage)
  const setActivePage = useDashboardStore((state) => state.setActivePage)
  const sidebarOpen = useDashboardStore((state) => state.sidebarOpen)
  const toggleSidebar = useDashboardStore((state) => state.toggleSidebar)

  return (
    <>
      {/* Mobile Overlay */}
      {sidebarOpen && (
        <div 
          className="fixed inset-0 bg-black/50 lg:hidden z-30"
          onClick={toggleSidebar}
        />
      )}

      {/* Sidebar */}
      <aside className={`
        fixed lg:static inset-y-0 left-0 w-64 bg-white dark:bg-slate-800 
        border-r border-gray-200 dark:border-slate-700 z-40 transform transition-transform
        ${sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
      `}>
        {/* Logo Section */}
        <div className="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
          <div>
            <h2 className="text-xl font-bold text-blue-600 dark:text-blue-400">SPECANCIENS</h2>
            <p className="text-xs text-gray-500 dark:text-gray-400">IAP Portal</p>
          </div>
          <button 
            onClick={toggleSidebar}
            className="lg:hidden p-1 hover:bg-gray-100 dark:hover:bg-slate-700 rounded"
          >
            <X size={20} />
          </button>
        </div>

        {/* Menu Items */}
        <nav className="p-4 space-y-2">
          {menuItems.map((item) => (
            <button
              key={item.id}
              onClick={() => {
                setActivePage(item.route)
                if (window.innerWidth < 1024) {
                  toggleSidebar()
                }
              }}
              className={`
                w-full flex items-center gap-3 px-4 py-3 rounded-lg font-medium
                transition-all duration-200 text-left
                ${activePage === item.route
                  ? 'bg-blue-600 text-white shadow-lg'
                  : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700'
                }
              `}
            >
              <span className="text-xl">{item.icon}</span>
              <span>{item.label}</span>
              {activePage === item.route && (
                <div className="ml-auto w-1 h-6 bg-white rounded"></div>
              )}
            </button>
          ))}
        </nav>

        {/* Footer Info */}
        <div className="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900">
          <p className="text-xs text-gray-500 dark:text-gray-400 text-center">
            SPECANCIENS v1.0<br />
            © 2026 Industry Awareness Program
          </p>
        </div>
      </aside>
    </>
  )
}
