<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\Link;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicHomeController extends Controller
{
    public function index(): View
    {
        $settings = SystemSetting::firstOrCreate(['id' => 1]);

        $services = Service::with('category')
            ->orderBy('name')
            ->take(8)
            ->get();

        $products = Product::query()
            ->active()
            ->with('category')
            ->orderByDesc('is_featured')
            ->orderByDesc('quantity')
            ->orderBy('name')
            ->take(6)
            ->get();

        $internetPackages = Service::with('category')
            ->where(function ($query): void {
                $query->whereHas('category', function ($q): void {
                    $q->where('name', 'like', '%internet%')
                      ->orWhere('name', 'like', '%wifi%');
                })->orWhere('name', 'like', '%internet%')
                  ->orWhere('name', 'like', '%wi-fi%')
                  ->orWhere('name', 'like', '%wifi%');
            })
            ->orderBy('base_price')
            ->take(3)
            ->get();

        $featuredServices = Service::with('category')
            ->where('status', 'active')
            ->where('is_featured', true)
            ->orderBy('name')
            ->get();

        $serviceCategories = ServiceCategory::where('is_active', true)
            ->withCount(['services' => function ($q) {
                $q->where('status', 'active');
            }])
            ->orderBy('name')
            ->get();

        $featuredProducts = Product::active()->featured()->with('category')->inStock()->take(10)->get();
        $latestProducts = Product::active()->with('category')->latest()->take(10)->get();
        $productCategories = \App\Models\ProductCategory::active()->orderBy('name')->get();

        $homepageServices = Service::where('status', 'active')->with('category')->orderByDesc('is_featured')->orderBy('name')->take(10)->get();

        // Dynamic content from database
        $testimonials = Testimonial::active()->ordered()->get();
        $quickLinks = Link::active()->group('quick_links')->ordered()->get();
        $serviceLinks = Link::active()->group('services')->ordered()->get();
        $footerLinks = Link::active()->group('footer')->ordered()->get();

        // Social media settings
        $socialLinks = collect();
        if ($settings->facebook_url) $socialLinks->push(['url' => $settings->facebook_url, 'icon' => 'bi-facebook', 'name' => 'Facebook']);
        if ($settings->twitter_url) $socialLinks->push(['url' => $settings->twitter_url, 'icon' => 'bi-twitter-x', 'name' => 'Twitter']);
        if ($settings->instagram_url) $socialLinks->push(['url' => $settings->instagram_url, 'icon' => 'bi-instagram', 'name' => 'Instagram']);
        if ($settings->linkedin_url) $socialLinks->push(['url' => $settings->linkedin_url, 'icon' => 'bi-linkedin', 'name' => 'LinkedIn']);
        if ($settings->youtube_url) $socialLinks->push(['url' => $settings->youtube_url, 'icon' => 'bi-youtube', 'name' => 'YouTube']);

        return view('home', [
            'services' => $services,
            'products' => $products,
            'internetPackages' => $internetPackages,
            'serviceOptions' => Service::orderBy('name')->get(),
            'fallbackServices' => $this->fallbackServices(),
            'fallbackProducts' => $this->fallbackProducts(),
            'fallbackPackages' => $this->fallbackPackages(),
            'testimonials' => $testimonials,
            'featuredServices' => $featuredServices,
            'serviceCategories' => $serviceCategories,
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
            'productCategories' => $productCategories,
            'homepageServices' => $homepageServices,
            // New dynamic data
            'quickLinks' => $quickLinks,
            'serviceLinks' => $serviceLinks,
            'footerLinks' => $footerLinks,
            'socialLinks' => $socialLinks,
            'settings' => $settings,
            'system_settings' => $settings,
        ]);
    }

    public function storeServiceRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'service_id' => ['required', 'exists:services,id'],
            'problem_description' => ['required', 'string', 'max:2000'],
            'problem_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $customer = Customer::updateOrCreate(
            ['phone' => $validated['phone']],
            [
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
            ]
        );

        $imagePath = $request->hasFile('problem_image')
            ? $request->file('problem_image')->store('service-requests', 'public')
            : null;

        ServiceRequest::create([
            'customer_id' => $customer->id,
            'service_id' => $validated['service_id'],
            'status' => 'pending',
            'notes' => $validated['problem_description'],
            'problem_image_path' => $imagePath,
        ]);

        return back()->with('success', 'Your service request has been submitted. Our team will contact you shortly.');
    }

    /**
     * Store contact form message from homepage.
     */
    public function storeContactMessage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create($validated);

        return back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');
    }

    private function fallbackServices(): array
    {
        return [
            ['title' => 'Software & IT Services', 'category' => 'Software', 'description' => 'Web systems, business apps, databases, and technical support.', 'icon' => 'bi-code-slash'],
            ['title' => 'Networking Solutions', 'category' => 'Networking', 'description' => 'LAN setup, router configuration, CCTV, and secure office networks.', 'icon' => 'bi-diagram-3'],
            ['title' => 'Repairs & Maintenance', 'category' => 'Repairs', 'description' => 'Computer, printer, phone, and device troubleshooting services.', 'icon' => 'bi-tools'],
            ['title' => 'Electrical Services', 'category' => 'Electrical', 'description' => 'Installations, maintenance, and small business electrical support.', 'icon' => 'bi-lightning-charge'],
            ['title' => 'Online Applications', 'category' => 'Applications', 'description' => 'Government, academic, business, and document application assistance.', 'icon' => 'bi-file-earmark-check'],
            ['title' => 'Printing & Stationery', 'category' => 'Stationery', 'description' => 'Printing, photocopying, binding, laminating, and office supplies.', 'icon' => 'bi-printer'],
            ['title' => 'Wi-Fi & Internet', 'category' => 'Internet', 'description' => 'Hotspot packages, Wi-Fi setup, and connectivity support.', 'icon' => 'bi-wifi'],
            ['title' => 'Cash Point Services', 'category' => 'Cash Point', 'description' => 'Mobile money, payment assistance, and cash point operations.', 'icon' => 'bi-cash-stack'],
        ];
    }

    private function fallbackProducts(): array
    {
        return [
            ['id' => 99991, 'slug' => 'flash-disk-32gb', 'name' => 'Flash Disk 32GB', 'selling_price' => 18000, 'price' => 18000, 'quantity' => 24, 'is_featured' => true, 'image' => 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&w=600&q=80', 'category_name' => 'Accessories'],
            ['id' => 99992, 'slug' => 'hdmi-cable', 'name' => 'HDMI Cable', 'selling_price' => 12000, 'price' => 12000, 'quantity' => 18, 'is_featured' => false, 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=600&q=80', 'category_name' => 'Accessories'],
            ['id' => 99993, 'slug' => 'wi-fi-router', 'name' => 'Wi-Fi Router', 'selling_price' => 85000, 'price' => 85000, 'quantity' => 7, 'is_featured' => true, 'image' => 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?auto=format&fit=crop&w=600&q=80', 'category_name' => 'Networking'],
            ['id' => 99994, 'slug' => 'office-printer', 'name' => 'Office Printer', 'selling_price' => 420000, 'price' => 420000, 'quantity' => 3, 'is_featured' => false, 'image' => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=600&q=80', 'category_name' => 'Peripherals'],
            ['id' => 99995, 'slug' => 'usb-keyboard', 'name' => 'USB Keyboard', 'selling_price' => 25000, 'price' => 25000, 'quantity' => 14, 'is_featured' => false, 'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=600&q=80', 'category_name' => 'Peripherals'],
            ['id' => 99996, 'slug' => 'computer-accessories', 'name' => 'Computer Accessories', 'selling_price' => 15000, 'price' => 15000, 'quantity' => 30, 'is_featured' => false, 'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&w=600&q=80', 'category_name' => 'Accessories'],
        ];
    }

    private function fallbackPackages(): array
    {
        return [
            ['name' => 'Daily Package', 'duration' => '24 hours', 'price' => 1000],
            ['name' => 'Weekly Package', 'duration' => '7 days', 'price' => 5000],
            ['name' => 'Monthly Package', 'duration' => '30 days', 'price' => 18000],
        ];
    }
}