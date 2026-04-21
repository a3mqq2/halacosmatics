@if($order->logs->isNotEmpty())
<div class="order-timeline">
    @foreach($order->logs as $log)
    <div class="timeline-item">
        <div class="timeline-dot bg-{{ match($log->action) {
            'created'   => 'success',
            'approved'  => 'primary',
            'rejected'  => 'danger',
            'shipped'   => 'info',
            'delivered' => 'success',
            'cancelled' => 'secondary',
            default     => 'secondary',
        } }}"></div>
        <div class="timeline-body">
            <div class="timeline-desc">{{ $log->description }}</div>
            <div class="timeline-time">{{ dt($log->created_at) }}</div>
        </div>
    </div>
    @endforeach
</div>
@else
<p class="text-muted text-center py-3 mb-0">لا يوجد سجل بعد.</p>
@endif
