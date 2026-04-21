<div class="row g-3">
    <div class="col-12 col-sm-6">
        <label class="form-label fw-semibold">اسم المستلم <span class="text-danger">*</span></label>
        <input type="text" name="recipient_name" class="form-control" required maxlength="100"
               value="{{ old('recipient_name') }}" placeholder="الاسم الكامل">
    </div>
    <div class="col-12 col-sm-6">
        <label class="form-label fw-semibold">التاريخ <span class="text-danger">*</span></label>
        <input type="date" name="date" class="form-control" required
               value="{{ old('date', now()->toDateString()) }}">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">الوصف <span class="text-danger">*</span></label>
        <input type="text" name="description" class="form-control" required maxlength="500"
               value="{{ old('description') }}" placeholder="وصف الحركة المالية">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">القيمة <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" name="amount" class="form-control" required min="0.01" step="0.01"
                   value="{{ old('amount') }}" placeholder="0.00">
            <span class="input-group-text fw-semibold">د.ل</span>
        </div>
    </div>
</div>
