import React from 'react'
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts'
import { enrollmentData } from '../data/mockData'

export default function EnrollmentChart() {
  return (
    <div className="bg-white dark:bg-slate-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-slate-700">
      <h2 className="text-lg font-bold text-gray-900 dark:text-white mb-4">Enrollment Trend (Last 30 Days)</h2>
      <ResponsiveContainer width="100%" height={300}>
        <LineChart data={enrollmentData}>
          <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
          <XAxis dataKey="month" stroke="#6b7280" />
          <YAxis stroke="#6b7280" />
          <Tooltip 
            contentStyle={{
              backgroundColor: '#1f2937',
              border: 'none',
              borderRadius: '8px',
              color: '#fff',
            }}
            formatter={(value) => `${value} students`}
          />
          <Line 
            type="monotone" 
            dataKey="students" 
            stroke="#2563eb" 
            strokeWidth={3}
            dot={{ fill: '#2563eb', r: 6 }}
            activeDot={{ r: 8 }}
          />
        </LineChart>
      </ResponsiveContainer>
    </div>
  )
}
