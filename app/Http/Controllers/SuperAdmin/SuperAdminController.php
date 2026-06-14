<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditTrail;
use App\Models\Backup;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockMovement;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Customer;
use App\Models\ContactMessage;
use App\Models\Link;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // ── User Stats ──
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $totalRoles = Role::count();
        $auditLogsCount = AuditTrail::count();

        // ── Inventory Stats ──
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'low_stock_alert_level')
            ->count();
        $outOfStockProducts = Product::where('quantity', '<=', 0)->count();
        $featuredProducts = Product::where('is_featured', true)->count();
        $totalCategories = ProductCategory::count();
        $totalStockMovements = StockMovement::count();

        // ── Service & Customer Stats ──
        $totalServices = Service::count();
        $activeServices = Service::where('status', 'active')->count();
        $serviceCategories = ServiceCategory::where('is_active', true)->count();
        $totalServiceRequests = ServiceRequest::count();
        $pendingServiceRequests = ServiceRequest::where('status', 'pending')->count();
        $totalCustomers = Customer::count();
        $totalContactMessages = ContactMessage::count();
        $unreadContactMessages = ContactMessage::where('is_approved', false)->count();

        // ── Content Stats ──
        $totalTestimonials = Testimonial::count();
        $totalLinks = Link::count();

        // ── Chart Data: Audit Logs (last 30 days) ──
        $auditChartLabels = [];
        $auditChartData = [];
        $today = now();
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i)->format('Y-m-d');
            $label = $today->copy()->subDays($i)->format('M d');
            $count = AuditTrail::whereDate('created_at', $date)->count();
            $auditChartLabels[] = $label;
            $auditChartData[] = $count;
        }

        // ── Chart Data: Service Requests by status ──
        $serviceRequestStatuses = ServiceRequest::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // ── Chart Data: Products by category ──
        $productsByCategory = ProductCategory::select('product_categories.name as category_name', DB::raw('count(products.id) as total'))
            ->leftJoin('products', 'product_categories.id', '=', 'products.category_id')
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderByDesc('total')
            ->take(8)
            ->get()
            ->pluck('total', 'category_name')
            ->toArray();

        // ── Chart Data: Stock movements last 30 days (in vs out) ──
        $stockMovementLabels = [];
        $stockInData = [];
        $stockOutData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i)->format('Y-m-d');
            $label = $today->copy()->subDays($i)->format('M d');
            $stockMovementLabels[] = $label;
            $stockInData[] = StockMovement::whereDate('created_at', $date)->where('type', 'in')->sum('quantity');
            $stockOutData[] = StockMovement::whereDate('created_at', $date)->where('type', 'out')->sum('quantity');
        }

        // ── Recent Activities ──
        $recentActivities = AuditTrail::latest()->take(10)->get();

        $data = compact(
            'totalUsers', 'activeUsers', 'totalRoles', 'auditLogsCount',
            'totalProducts', 'lowStockProducts', 'outOfStockProducts', 'featuredProducts',
            'totalCategories', 'totalStockMovements',
            'totalServices', 'activeServices', 'serviceCategories',
            'totalServiceRequests', 'pendingServiceRequests',
            'totalCustomers', 'totalContactMessages', 'unreadContactMessages',
            'totalTestimonials', 'totalLinks',
            'auditChartLabels', 'auditChartData',
            'serviceRequestStatuses',
            'productsByCategory',
            'stockMovementLabels', 'stockInData', 'stockOutData',
            'recentActivities'
        );

        return view('super-admin.dashboard', $data);
    }

    // Operations
    public function operationsDashboard(): View
    {
        return view('super-admin.operations.dashboard');
    }

    public function activityTracking(): View
    {
        return view('super-admin.operations.tracking');
    }

    // Cash Point Setup
    public function cashPointAccounts(): View
    {
        return view('super-admin.cash-points.accounts');
    }

    public function openingBalanceSetup(): View
    {
        return view('super-admin.cash-points.opening-balance');
    }

    public function closingBalanceReports(): View
    {
        return view('super-admin.cash-points.closing-reports');
    }

    public function transactionCategories(): View
    {
        return view('super-admin.cash-points.categories');
    }

    // Services Management
    public function addServices(): View
    {
        return view('super-admin.services.add');
    }

    public function editServices(): View
    {
        return view('super-admin.services.edit');
    }

    public function serviceCategories(): View
    {
        return view('super-admin.services.categories');
    }

    // Inventory
    public function products(): View
    {
        return view('super-admin.inventory.products');
    }

    public function stockIn(): View
    {
        return view('super-admin.inventory.stock-in');
    }

    public function stockOut(): View
    {
        return view('super-admin.inventory.stock-out');
    }

    public function stockReports(): View
    {
        return view('super-admin.inventory.reports');
    }

    // System Administration
    public function systemOverview(): View
    {
        return view('super-admin.system.overview');
    }

    public function systemConfiguration(): View
    {
        return view('super-admin.system.configuration');
    }

    // Notifications
    public function systemNotifications(): View
    {
        return view('super-admin.notifications.system');
    }

    public function userAlerts(): View
    {
        return view('super-admin.notifications.alerts');
    }

    // System Maintenance
    public function clearCache(Request $request)
    {
        Artisan::call('optimize:clear');
        
        return redirect()->back()->with('success', 'Cache cleared successfully.');
    }

    public function optimizeSystem(Request $request)
    {
        Artisan::call('optimize');
        
        return redirect()->back()->with('success', 'System optimized successfully.');
    }

    public function maintenanceMode(Request $request)
    {
        $mode = $request->input('mode', 'down');
        
        if ($mode === 'down') {
            Artisan::call('down');
            return redirect()->back()->with('success', 'Maintenance mode enabled.');
        } else {
            Artisan::call('up');
            return redirect()->back()->with('success', 'Maintenance mode disabled.');
        }
    }
}