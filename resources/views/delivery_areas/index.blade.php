@extends('layouts.app')

@section('title', 'مناطق التوصيل')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">مناطق التوصيل</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="fw-bold mb-0" style="color:#4a2619">
        <i class="ti ti-map-pin me-1"></i> مناطق التوصيل
    </h5>
    <a href="{{ route('delivery_areas.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus me-1"></i> منطقة جديدة
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 py-2 px-3 mb-3" style="font-size:.9rem">
    {{ session('success') }}
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#fdf8f6;border-bottom:2px solid #f0e8e3">
                <tr>
                    <th class="ps-4 py-3 text-muted fw-semibold" style="font-size:.82rem">#</th>
                    <th class="py-3 text-muted fw-semibold" style="font-size:.82rem">المنطقة</th>
                    <th class="py-3 text-muted fw-semibold" style="font-size:.82rem">سعر التوصيل</th>
                    <th class="pe-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($areas as $area)
                <tr>
                    <td class="ps-4 text-muted" style="font-size:.82rem">{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $area->name }}</td>
                    <td>
                        <span class="badge rounded-pill" style="background:#fdf8f6;color:#4a2619;border:1px solid #e8d5cc;font-size:.85rem;font-weight:700">
                            {{ $area->price }} د.ل
                        </span>
                    </td>
                    <td class="pe-4 text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('delivery_areas.edit', $area) }}" class="btn btn-sm btn-outline-secondary" style="font-size:.8rem">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('delivery_areas.destroy', $area) }}"
                                  onsubmit="return confirm('حذف منطقة {{ addslashes($area->name) }}؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size:.8rem">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-5">لا توجد مناطق مضافة بعد</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
