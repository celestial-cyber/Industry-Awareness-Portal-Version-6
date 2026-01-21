export const dashboardMetrics = {
  totalStudents: 1247,
  activeSessions: 23,
  completionRate: 78,
  pendingRequests: 15,
  studentsTrend: '+12%',
  sessionsTrend: '+8%',
  completionTrend: '+5%',
  requestsTrend: '+3%',
}

export const recentActivity = [
  {
    id: 1,
    type: 'registration',
    student: 'John Doe',
    module: 'AI Fundamentals',
    time: '2 min ago',
    icon: '👤',
  },
  {
    id: 2,
    type: 'completion',
    student: 'Sarah Johnson',
    module: 'Cybersecurity Basics',
    time: '5 min ago',
    icon: '✅',
  },
  {
    id: 3,
    type: 'request',
    student: '3 new students',
    module: 'Blockchain Development',
    time: '10 min ago',
    icon: '📝',
  },
  {
    id: 4,
    type: 'registration',
    student: 'Mike Smith',
    module: 'Cloud Computing',
    time: '15 min ago',
    icon: '👤',
  },
  {
    id: 5,
    type: 'completion',
    student: 'Emma Davis',
    module: 'Web Development',
    time: '22 min ago',
    icon: '✅',
  },
]

export const enrollmentData = [
  { month: 'Jan 1', students: 120 },
  { month: 'Jan 6', students: 145 },
  { month: 'Jan 11', students: 167 },
  { month: 'Jan 16', students: 189 },
  { month: 'Today', students: 234 },
]

export const moduleCompletionData = [
  { name: 'Year 1', value: 45, fill: '#3b82f6' },
  { name: 'Year 2', value: 25, fill: '#10b981' },
  { name: 'Year 3', value: 20, fill: '#f59e0b' },
  { name: 'Year 4', value: 10, fill: '#ef4444' },
]

export const notifications = [
  { id: 1, message: 'New registration from Rahul Kumar', time: '2 min ago', read: false },
  { id: 2, message: 'Session "AI Fundamentals" starts tomorrow', time: '1 hour ago', read: false },
  { id: 3, message: 'Module completion rate reached 78%', time: '3 hours ago', read: true },
  { id: 4, message: 'New feedback from students on UX', time: '1 day ago', read: true },
  { id: 5, message: 'System maintenance scheduled for tonight', time: '2 days ago', read: true },
  { id: 6, message: 'Analytics report is ready for download', time: '3 days ago', read: true },
  { id: 7, message: 'New admin account created', time: '1 week ago', read: true },
  { id: 8, message: 'Backup completed successfully', time: '1 week ago', read: true },
]

export const quickActions = [
  { id: 1, label: 'Create Session', icon: '➕', color: 'blue' },
  { id: 2, label: 'View Registrations', icon: '📋', color: 'green' },
  { id: 3, label: 'Approve Requests', icon: '✅', color: 'yellow' },
  { id: 4, label: 'Export Reports', icon: '📥', color: 'purple' },
]

export const menuItems = [
  { id: 1, label: 'Home', icon: '🏠', route: 'home' },
  { id: 2, label: 'Create Session', icon: '➕', route: 'sessions' },
  { id: 3, label: 'View Registrations', icon: '📋', route: 'registrations' },
  { id: 4, label: 'Analytics & Reports', icon: '📊', route: 'analytics' },
  { id: 5, label: 'Manage Modules', icon: '📚', route: 'modules' },
  { id: 6, label: 'Form Requests', icon: '✉️', route: 'requests' },
  { id: 7, label: 'Notifications', icon: '🔔', route: 'notifications' },
  { id: 8, label: 'Settings', icon: '⚙️', route: 'settings' },
]
