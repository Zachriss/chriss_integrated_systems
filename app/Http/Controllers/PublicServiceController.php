<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\View\View;

class PublicServiceController extends Controller
{
    public function index(): View
    {
        $categories = ServiceCategory::where('is_active', true)
            ->withCount(['services' => function ($q) {
                $q->where('status', 'active');
            }])
            ->orderBy('name')
            ->get();

        $services = Service::with('category', 'createdBy')
            ->where('status', 'active')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12);

        $featuredServices = Service::with('category')
            ->where('status', 'active')
            ->where('is_featured', true)
            ->orderBy('name')
            ->get();

        return view('services.index', compact('categories', 'services', 'featuredServices'));
    }

    public function show(string $slug): View
    {
        $service = Service::with('category', 'createdBy')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $relatedServices = Service::with('category')
            ->where('status', 'active')
            ->where('id', '!=', $service->id)
            ->where(function ($q) use ($service) {
                if ($service->category_id) {
                    $q->where('category_id', $service->category_id);
                }
            })
            ->orderByDesc('is_featured')
            ->take(4)
            ->get();

        return view('services.show', compact('service', 'relatedServices'));
    }
}