import React from 'react'
import MetricCard from './MetricCard'
import RecentActivity from './RecentActivity'
import EnrollmentChart from './EnrollmentChart'
import CompletionChart from './CompletionChart'
import QuickActions from './QuickActions'
import { dashboardMetrics } from '../data/mockData'

export default function DashboardHome() {
  return (
    <div className="p-6 space-y-6 max-w-7xl mx-auto">
      {/* Metrics Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <MetricCard 
          title="Total Students" 
          value={dashboardMetrics.totalStudents.toLocaleString()} 
          trend={dashboardMetrics.studentsTrend}
          icon="👥"
          trendPositive={true}
        />
        <MetricCard 
          title="Active Sessions" 
          value={dashboardMetrics.activeSessions} 
          trend={dashboardMetrics.sessionsTrend}
          icon="🔥"
          trendPositive={true}
        />
        <MetricCard 
          title="Completion Rate" 
          value={`${dashboardMetrics.completionRate}%`} 
          trend={dashboardMetrics.completionTrend}
          icon="✅"
          trendPositive={true}
        />
        <MetricCard 
          title="Pending Requests" 
          value={dashboardMetrics.pendingRequests} 
          trend={dashboardMetrics.requestsTrend}
          icon="⏳"
          trendPositive={false}
        />
      </div>

      {/* Main Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left Column - Recent Activity */}
        <div className="lg:col-span-1">
          <RecentActivity />
        </div>

        {/* Middle Column - Enrollment Chart */}
        <div className="lg:col-span-2">
          <EnrollmentChart />
        </div>
      </div>

      {/* Bottom Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Quick Actions */}
        <div className="lg:col-span-1">
          <QuickActions />
        </div>

        {/* Completion Chart */}
        <div className="lg:col-span-2">
          <CompletionChart />
        </div>
      </div>
    </div>
  )
}
