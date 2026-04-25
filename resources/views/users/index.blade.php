@extends('layouts.app')

@section('title', 'المستخدمين')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">المستخدمين</li>
@endsection

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0 fw-bold">
            <i class="ti ti-users me-1"></i> المستخدمين
            <span class="badge bg-secondary ms-1">{{ $users->total() }}</span>
        </h5>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus me-1"></i> إضافة مستخدم
        </a>
    </div>

    {{-- Desktop Table --}}
    <div class="card d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>رقم الهاتف</th>
                        <th class="text-center">مستخدمين</th>
                        <th class="text-center">مسوقين</th>
                        <th class="text-center">منتجات</th>
                        <th class="text-center">مدير عام</th>
                        <th>تاريخ الإضافة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-muted small">{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-primary text-white rounded-circle fs-14 fw-bold">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    @if($user->id === Auth::id())
                                        <span class="badge bg-primary-subtle text-primary" style="font-size:.7rem">أنت</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><code class="text-muted">{{ $user->phone }}</code></td>
                        <td class="text-center">
                            @if($user->is_super || $user->perm_users)
                                <i class="ti ti-circle-check text-success fs-5"></i>
                            @else
                                <i class="ti ti-circle-x text-muted fs-5"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->is_super || $user->perm_marketers)
                                <i class="ti ti-circle-check text-success fs-5"></i>
                            @else
                                <i class="ti ti-circle-x text-muted fs-5"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->is_super || $user->perm_products)
                                <i class="ti ti-circle-check text-success fs-5"></i>
                            @else
                                <i class="ti ti-circle-x text-muted fs-5"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->is_super)
                                <span class="badge bg-warning-subtle text-warning">مدير عام</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $user->created_at->format('Y/m/d') }}</td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="تعديل">
                                    <i class="ti ti-edit"></i>
                                </a>
                                @if($user->id != Auth::id())
                                <button type="button" class="btn btn-sm btn-outline-danger" title="حذف"
                                        onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                    <i class="ti ti-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="ti ti-users-off fs-2 d-block mb-2"></i>
                            لا يوجد مستخدمون
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="d-md-none">
        @forelse($users as $user)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary text-white rounded-circle fs-14 fw-bold">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ $user->name }}</div>
                        <code class="text-muted small">{{ $user->phone }}</code>
                    </div>
                    @if($user->is_super)
                        <span class="badge bg-warning-subtle text-warning">مدير عام</span>
                    @endif
                </div>

                <div class="d-flex gap-2 flex-wrap mb-3">
                    <span class="badge {{ ($user->is_super || $user->perm_users) ? 'bg-success-subtle text-success' : 'bg-light text-muted' }}">
                        <i class="ti ti-users me-1"></i> مستخدمين
                    </span>
                    <span class="badge {{ ($user->is_super || $user->perm_marketers) ? 'bg-success-subtle text-success' : 'bg-light text-muted' }}">
                        <i class="ti ti-user-star me-1"></i> مسوقين
                    </span>
                    <span class="badge {{ ($user->is_super || $user->perm_products) ? 'bg-success-subtle text-success' : 'bg-light text-muted' }}">
                        <i class="ti ti-box me-1"></i> منتجات
                    </span>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="ti ti-edit me-1"></i> تعديل
                    </a>
                    @if($user->id != Auth::id())
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                        <i class="ti ti-trash"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
            <i class="ti ti-users-off fs-2 d-block mb-2"></i>
            لا يوجد مستخدمون
        </div>
        @endforelse
    </div>

    @if($users->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $users->links() }}
        </div>
    @endif

    <form id="deleteForm" method="POST" style="display:none">
        @csrf @method('DELETE')
    </form>

@endsection

@push('scripts')
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'حذف المستخدم',
            html: `هل أنت متأكد من حذف <strong>${name}</strong>؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `/users/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush
