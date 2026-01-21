import React from 'react'
import { quickActions } from '../data/mockData'

const colorClasses = {
  blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50',
  green: 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50',
  yellow: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/50',
  purple: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 hover:bg-purple-200 dark:hover:bg-purple-900/50',
}

export default function QuickActions() {
  return (
    <div className="bg-white dark:bg-slate-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-slate-700">
      <h2 className="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
      <div className="grid grid-cols-1 gap-3">
        {quickActions.map((action) => (
          <button
            key={action.id}
            className={`flex items-center gap-3 p-4 rounded-lg font-medium transition-all ${colorClasses[action.color]}`}
          >
            <span className="text-2xl">{action.icon}</span>
            <span>{action.label}</span>
            <span className="ml-auto">→</span>
          </button>
        ))}
      </div>
    </div>
  )
}
