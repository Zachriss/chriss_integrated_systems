@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('customer.services.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Services</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                            <i class="bi bi-gear fs-3"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">{{ $service->name }}</h4>
                            <small class="text-muted">{{ $service->category->name ?? 'No category' }}</small>
                        </div>
                    </div>
                    <p>{{ $service->short_description }}</p>
                    @if($service->description)
                        <hr>
                        <div class="text-muted">{!! nl2br(e($service->description)) !!}</div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="text-success mb-0">TZS {{ number_format($service->base_price ?? 0, 2) }}</h4>
                        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#requestModal">
                            <i class="bi bi-send me-1"></i> Request Service
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Request Modal --}}
<div class="modal fade" id="requestModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Request: {{ $service->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form id="requestForm">
        <input type="hidden" name="service_id" value="{{ $service->id }}">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Describe what you need *</label>
                <textarea name="description" class="form-control" rows="4" required placeholder="Explain the service you need..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="submitReqBtn">Submit Request</button>
        </div>
    </form>
</div></div></div>
@endsection

@section('scripts')
<script>
document.getElementById('requestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitReqBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Submitting...';
    const data = Object.fromEntries(new FormData(this).entries());
    fetch('{{ route("customer.service-requests.store") }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},
        body: JSON.stringify(data)
    }).then(r=>r.json()).then(d=>{
        btn.disabled=false; btn.innerHTML='Submit Request';
        if(d.success){
            if(typeof showSystemAlert==='function') showSystemAlert({theme:'success',title:'Submitted',text:d.message,timer:2000,showConfirmButton:false});
            bootstrap.Modal.getInstance(document.getElementById('requestModal')).hide();
            document.getElementById('requestForm').reset();
        } else { alert(d.message||'Error'); }
    }).catch(()=>{btn.disabled=false;btn.innerHTML='Submit Request';alert('Error');});
});
</script>
@endsection