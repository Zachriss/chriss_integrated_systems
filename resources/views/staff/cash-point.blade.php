@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <h4 class="mb-4"><i class="bi bi-cash-stack me-2"></i>Daily Cash Point</h4>
    <p class="text-muted">Record opening and closing balances for each channel.</p>

    <div class="row g-3 mt-2">
        @foreach($channelData as $cd)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-wallet2 me-2"></i>{{ $cd->channel->name }}</h5>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small text-muted">Opening Balance (TZS)</label>
                            @if($cd->is_opened)
                                <div class="fw-bold fs-5 text-primary">{{ number_format($cd->opening_balance, 0) }}</div>
                                <span class="badge bg-secondary"><i class="bi bi-lock-fill me-1"></i>Locked</span>
                            @else
                                <input type="number" class="form-control form-control-sm opening-input" 
                                       data-channel-id="{{ $cd->channel->id }}" 
                                       data-channel-name="{{ $cd->channel->name }}"
                                       min="0" step="0.01" placeholder="Enter opening">
                            @endif
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Closing Balance (TZS)</label>
                            @if($cd->is_closed)
                                <div class="fw-bold fs-5 text-success">{{ number_format($cd->closing_balance, 0) }}</div>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Done</span>
                                @if($cd->difference != 0)
                                    <br><small class="text-{{ $cd->difference >= 0 ? 'success' : 'danger' }}">Change: {{ number_format($cd->difference, 0) }}</small>
                                @endif
                            @else
                                <input type="number" class="form-control form-control-sm closing-input"
                                       data-channel-id="{{ $cd->channel->id }}"
                                       data-channel-name="{{ $cd->channel->name }}"
                                       min="0" step="0.01" placeholder="Enter closing"
                                       {{ !$cd->is_opened ? 'disabled' : '' }}>
                                @if(!$cd->is_opened)
                                    <small class="text-muted">Set opening first</small>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if(!$cashPoint)
    <div class="text-center py-4 mt-3">
        <p class="text-muted">Enter opening balances above to start. They will be locked once saved.</p>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Opening balance - save on Enter or blur
document.querySelectorAll('.opening-input').forEach(input => {
    input.addEventListener('change', function() {
        const val = parseFloat(this.value);
        if (!val || val < 0) return;
        const btn = this;
        btn.disabled = true;
        btn.style.opacity = '0.6';
        
        fetch('{{ route("staff.cash-point.opening") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                channel_id: this.dataset.channelId,
                opening_balance: val
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error');
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.style.opacity = '1';
            alert('Error saving opening balance');
        });
    });
});

// Closing balance - save on Enter or blur
document.querySelectorAll('.closing-input').forEach(input => {
    input.addEventListener('change', function() {
        const val = parseFloat(this.value);
        if (!val || val < 0) return;
        const btn = this;
        btn.disabled = true;
        btn.style.opacity = '0.6';
        
        fetch('{{ route("staff.cash-point.closing") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                channel_id: this.dataset.channelId,
                closing_balance: val
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error');
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.style.opacity = '1';
            alert('Error saving closing balance');
        });
    });
});
</script>
@endsection