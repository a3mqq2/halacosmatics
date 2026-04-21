<?php

namespace App\DTOs;

/**
 * يمثل بيانات فشل محاولة تسليم الطلب.
 *
 * reason  : العلة المختارة من القائمة المعرفة (phone_off | wrong_number | forwarded | didnt_order | busy | other)
 * notes   : النص الحر — إلزامي عند اختيار "other"، اختياري في بقية الحالات
 * label   : تسمية العلة بالعربية جاهزة للحفظ في اللوق
 */
readonly class FailDeliveryData
{
    public string $label;

    private const LABELS = [
        'phone_off'    => 'رقم هاتف مغلق',
        'wrong_number' => 'رقم هاتف غير صحيح',
        'forwarded'    => 'تحويل مكالمات',
        'didnt_order'  => 'لم يقم بالطلب',
        'busy'         => 'الرقم مشغول',
        'other'        => 'أخرى',
    ];

    public function __construct(
        public readonly string  $reason,
        public readonly ?string $notes,
    ) {
        $base        = self::LABELS[$reason] ?? $reason;
        $this->label = ($reason === 'other' && $notes)
            ? "أخرى — {$notes}"
            : ($notes ? "{$base} — {$notes}" : $base);
    }

    public static function reasons(): array
    {
        return self::LABELS;
    }
}
