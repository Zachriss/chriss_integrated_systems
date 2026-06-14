@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Assign New Task</h4>
            <p class="text-muted mb-0 small">Create a task assignment for a staff member</p>
        </div>
        <a href="{{ route('admin.staff-tasks.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Tasks
        </a>
    </div>

    <div class="row g-4">
        {{-- MAIN FORM --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.staff-tasks.store') }}" method="POST" id="taskForm">
                        @csrf

                        {{-- Staff Selection with live preview --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-person-badge me-1"></i> Staff Member <span class="text-danger">*</span>
                            </label>
                            <select name="staff_id" id="staffSelect" class="form-select @error('staff_id') is-invalid @enderror" required>
                                <option value="">— Select Staff Member —</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ old('staff_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                @endforeach
                            </select>
                            @error('staff_id') <small class="text-danger">{{ $message }}</small> @enderror
                            <div id="staffPreview" class="mt-2 d-none">
                                <div class="alert alert-info py-2 mb-0 small">
                                    <i class="bi bi-person me-1"></i> Assigning to: <strong id="staffPreviewName"></strong>
                                </div>
                            </div>
                        </div>

                        {{-- Category Only --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i> Category <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" id="categorySelect" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">— Select Category —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" data-service-count="{{ $cat->services->count() }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }} ({{ $cat->services->count() }} services)</option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                            <div id="categoryPreview" class="mt-2 d-none">
                                <div class="alert alert-info py-2 mb-0 small">
                                    <i class="bi bi-info-circle me-1"></i> All services in this category will be available for income recording.
                                </div>
                            </div>
                        </div>

                        {{-- Task Details --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-card-heading me-1"></i> Task Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="taskTitle" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" required maxlength="255" placeholder="e.g., Process daily M-Pesa transactions">
                            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                            <small class="text-muted">Auto-filled from category name. <span id="titleCount">0</span>/255</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-text-paragraph me-1"></i> Description
                            </label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" placeholder="Provide any additional instructions or details...">{{ old('description') }}</textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar me-1"></i> Task Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                                   value="{{ old('date', today()->toDateString()) }}" required>
                            @error('date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i> Assign Task
                            </button>
                            <a href="{{ route('admin.staff-tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- SIDEBAR INFO --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-1"></i> Assignment Tips</h6>
                </div>
                <div class="card-body small">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Be specific in the task title so the staff knows exactly what to do.</li>
                        <li class="mb-2">Use the description to add deadlines, links, or special instructions.</li>
                                <li class="mb-2">Select the correct category for accurate reporting.</li>
                        <li>Tasks default to <span class="badge bg-warning">Pending</span> status upon creation.</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-eyeglasses me-1"></i> Task Preview</h6>
                </div>
                <div class="card-body" id="previewCard">
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-file-text" style="font-size:2rem;"></i>
                        <p class="mt-2 mb-0 small">Fill in the form to see a preview</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function autoFillTitleFromCategory() {
    const catSelect = document.getElementById('categorySelect');
    const titleInput = document.getElementById('taskTitle');
    if (!catSelect.value) return;
    const catOption = catSelect.options[catSelect.selectedIndex];
    const catName = catOption.text.split('(')[0].trim();
    if (!titleInput.value || titleInput.dataset.lastCategory) {
        titleInput.value = catName;
        titleInput.dataset.lastCategory = catName;
        document.getElementById('titleCount').textContent = catName.length;
    }
}

// Category info preview - also auto-fill task title from category name
document.getElementById('categorySelect').addEventListener('change', function(){
    const preview = document.getElementById('categoryPreview');
    const titleInput = document.getElementById('taskTitle');
    if (this.value) {
        preview.classList.remove('d-none');
        autoFillTitleFromCategory();
    } else {
        preview.classList.add('d-none');
        titleInput.dataset.lastCategory = '';
    }
    updatePreview();
});

// Staff preview
document.getElementById('staffSelect').addEventListener('change', function(){
    const preview = document.getElementById('staffPreview');
    const nameEl = document.getElementById('staffPreviewName');
    const option = this.options[this.selectedIndex];
    if (option.value) {
        nameEl.textContent = option.text;
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
    }
    updatePreview();
});

// Title character count
document.getElementById('taskTitle').addEventListener('input', function(){
    document.getElementById('titleCount').textContent = this.value.length;
});

// Live preview
document.getElementById('taskTitle').addEventListener('input', updatePreview);
document.getElementById('staffSelect').addEventListener('change', updatePreview);
document.getElementById('categorySelect').addEventListener('change', updatePreview);

// Auto-fill on page load if old category is selected (after validation errors)
document.addEventListener('DOMContentLoaded', function() {
    autoFillTitleFromCategory();
});

function updatePreview() {
    const title = document.getElementById('taskTitle').value.trim();
    const staff = document.getElementById('staffSelect');
    const cat = document.getElementById('categorySelect');
    const card = document.getElementById('previewCard');

    if (!title && !staff.value) {
        card.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="bi bi-file-text" style="font-size:2rem;"></i>
                <p class="mt-2 mb-0 small">Fill in the form to see a preview</p>
            </div>`;
        return;
    }

    const catOption = cat.options[cat.selectedIndex];
    const serviceCount = cat.value ? (catOption.dataset.serviceCount || 'all') : '(Not selected)';

    card.innerHTML = `
        <div class="border rounded p-3">
            <h6 class="fw-bold mb-1">${title || '(No title)'}</h6>
            <div class="small text-muted">
                <div><i class="bi bi-person me-1"></i> ${staff.value ? staff.options[staff.selectedIndex].text : '(Not selected)'}</div>
                <div><i class="bi bi-tag me-1"></i> ${cat.value ? catOption.text : '(Not selected)'}</div>
                <div><i class="bi bi-gear me-1"></i> ${cat.value ? serviceCount + ' services available' : ''}</div>
            </div>
            <div class="mt-2"><span class="badge bg-warning">Pending</span></div>
        </div>
    `;
}
</script>
@endsection