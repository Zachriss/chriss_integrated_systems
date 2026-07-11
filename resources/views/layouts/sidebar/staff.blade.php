<div class="sidebar-section">
    <small>Staff Menu</small>
</div>
@php
    $sidebarUser = auth()->user();
    $assignedCategories = $sidebarUser ? ($sidebarUser->assignedCategories ?? collect([])) : collect([]);
    $tasksByCategory = collect([]);
    $allTaskCategories = collect([]);
    if ($sidebarUser) {
        $userId = $sidebarUser->id;
        // Get ALL tasks assigned to this staff member (not filtered by assigned categories)
        $allTasks = \App\Models\StaffTask::where('staff_id', $userId)
            ->with('category')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(50)
            ->get();
        // Group tasks by category_id
        $tasksByCategory = $allTasks->groupBy(function($task) {
            return $task->category_id ?? 'uncategorized';
        });
        // Collect categories that have tasks (merge assigned categories + categories from tasks)
        $allTaskCategories = $allTasks->pluck('category')->filter()->unique('id');
        // Merge with assigned categories to have full list - deduplicate by id
        $mergedCategories = $assignedCategories->merge($allTaskCategories)->keyBy('id');
        $assignedCategories = $mergedCategories;
    }
@endphp
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span class="nav-text">Dashboard</span>
        </a>
    </li>
    <li class="nav-item has-submenu">
        <a href="#" class="nav-link nav-link-toggle {{ request()->routeIs('staff.task-assignments.*') || request()->routeIs('staff.tasks.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#staffAssignedCategoriesSubmenu" aria-expanded="{{ request()->routeIs('staff.task-assignments.*') || request()->routeIs('staff.tasks.*') ? 'true' : 'false' }}">
            <i class="bi bi-list-check"></i>
            <span class="nav-text">My Tasks</span>
            <i class="bi bi-chevron-down submenu-arrow ms-auto"></i>
        </a>
        <div class="collapse submenu-collapse {{ request()->routeIs('staff.task-assignments.*') || request()->routeIs('staff.tasks.*') ? 'show' : '' }}" id="staffAssignedCategoriesSubmenu">
            <ul class="nav flex-column submenu-nav">
                @forelse($assignedCategories as $category)
                    <li class="nav-item">
                        <span class="nav-link submenu-link disabled-link" style="cursor: default; opacity: 1;">
                            <i class="bi bi-folder2-open"></i>
                            <span class="nav-text submenu-text">{{ \Illuminate\Support\Str::limit($category->name, 28) }}</span>
                        </span>
                    </li>
                    @if(isset($tasksByCategory[$category->id]) && $tasksByCategory[$category->id]->count())
                        @foreach($tasksByCategory[$category->id] as $task)
                            <li class="nav-item">
                                <a href="{{ route('staff.tasks.show', $task) }}" class="nav-link submenu-link submenu-task-link {{ request()->routeIs('staff.tasks.show') && request()->route('task') && request()->route('task')->id == $task->id ? 'active' : '' }}" title="{{ $task->title }}">
                                    <span class="task-status-dot status-{{ $task->status }}"></span>
                                    <span class="nav-text submenu-text">{{ \Illuminate\Support\Str::limit($task->title, 22) }}</span>
                                </a>
                            </li>
                        @endforeach
                    @endif
                @empty
                    <li class="nav-item">
                        <span class="nav-link submenu-link disabled-link">
                            <span class="nav-text submenu-text text-muted">No categories assigned</span>
                        </span>
                    </li>
                @endforelse
                <li class="nav-item">
                    <a href="{{ route('staff.tasks.index') }}" class="nav-link submenu-link view-all-link {{ request()->routeIs('staff.tasks.index') ? 'active' : '' }}">
                        <i class="bi bi-eye"></i>
                        <span class="nav-text submenu-text">View All Tasks</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a href="{{ route('staff.service-requests') }}" class="nav-link {{ request()->routeIs('staff.service-requests*') ? 'active' : '' }}">
            <i class="bi bi-inbox"></i>
            <span class="nav-text">Customer Orders</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('staff.services') }}" class="nav-link {{ request()->routeIs('staff.services') && !request()->routeIs('staff.service-requests') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span class="nav-text">My Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('staff.inventory') }}" class="nav-link {{ request()->routeIs('staff.inventory*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span class="nav-text">Inventory</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('staff.income.history') }}" class="nav-link {{ request()->routeIs('staff.income.history') || request()->routeIs('staff.income.edit') ? 'active' : '' }}">
            <i class="bi bi-currency-exchange"></i>
            <span class="nav-text">Income History</span>
        </a>
    </li>
    <li class="nav-item has-submenu">
        <a href="#" class="nav-link nav-link-toggle {{ request()->routeIs('staff.cashpoint*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#staffCashpointSubmenu" aria-expanded="{{ request()->routeIs('staff.cashpoint*') ? 'true' : 'false' }}">
            <i class="bi bi-cash-stack"></i>
            <span class="nav-text">Cash Point</span>
            <i class="bi bi-chevron-down submenu-arrow ms-auto"></i>
        </a>
        <div class="collapse submenu-collapse {{ request()->routeIs('staff.cashpoint*') ? 'show' : '' }}" id="staffCashpointSubmenu">
            <ul class="nav flex-column submenu-nav">
                <li class="nav-item">
                    <a href="{{ route('staff.cashpoint.dashboard') }}" class="nav-link submenu-link {{ request()->routeIs('staff.cashpoint.dashboard') ? 'active' : '' }}">
                        <span class="nav-text submenu-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('staff.cashpoint.history') }}" class="nav-link submenu-link {{ request()->routeIs('staff.cashpoint.history') ? 'active' : '' }}">
                        <span class="nav-text submenu-text">Transaction History</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('staff.cashpoint.closing.create') }}" class="nav-link submenu-link {{ request()->routeIs('staff.cashpoint.closing.create') ? 'active' : '' }}">
                        <span class="nav-text submenu-text">End of Day Closing</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>

<div class="sidebar-section">
    <small>Account</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('dashboard.notifications.index') }}" class="nav-link {{ request()->routeIs('dashboard.notifications.*') ? 'active' : '' }}">
            <i class="bi bi-bell-fill"></i>
            <span class="nav-text">Notifications</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('dashboard.profile.show') }}" class="nav-link {{ request()->routeIs('dashboard.profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>
            <span class="nav-text">Profile</span>
        </a>
    </li>
</ul>
