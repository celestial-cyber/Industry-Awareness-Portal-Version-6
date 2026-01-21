import React from 'react'
import { recentActivity } from '../data/mockData'

export default function RecentActivity() {
  return (
    <div className="bg-white dark:bg-slate-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-slate-700 h-full">
      <h2 className="text-lg font-bold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
      <div className="space-y-3 overflow-y-auto max-h-96">
        {recentActivity.map((activity) => (
          <div 
            key={activity.id}
            className="flex items-start gap-3 pb-3 border-b border-gray-100 dark:border-slate-700 last:border-0 hover:bg-gray-50 dark:hover:bg-slate-700/50 p-2 rounded transition"
          >
            <span className="text-2xl mt-1">{activity.icon}</span>
            <div className="flex-1 min-w-0">
              <p className="text-sm font-medium text-gray-900 dark:text-white truncate">
                {activity.student}
              </p>
              <p className="text-sm text-gray-600 dark:text-gray-400 truncate">
                {activity.type === 'registration' && 'Registered for'}
                {activity.type === 'completion' && 'Completed'}
                {activity.type === 'request' && 'Requested'} {activity.module}
              </p>
              <p className="text-xs text-gray-500 dark:text-gray-500 mt-1">{activity.time}</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}
