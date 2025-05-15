<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'type',
        'start_date',
        'end_date',
        'days_taken',
        'reason',
        'status',
        'approved_by',
        'notes'
    ];

    // أنواع الإجازات
    public const TYPE_ANNUAL = 'annual';
    public const TYPE_SICK = 'sick';
    public const TYPE_UNPAID = 'unpaid';
    public const TYPE_OTHER = 'other';

    // حالات الطلب
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    /**
     * علاقة المستخدم الذي قدم الطلب
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة القسم التابع له الطلب
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * علاقة المستخدم الذي وافق على الطلب
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * الحصول على أنواع الإجازات المتاحة
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_ANNUAL => 'Annual Leave',
            self::TYPE_SICK => 'Sick Leave',
            self::TYPE_UNPAID => 'Unpaid Leave',
            self::TYPE_OTHER => 'Other Leave'
        ];
    }

    /**
     * الحصول على حالات الطلب المتاحة
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected'
        ];
    }

    /**
     * التحقق مما إذا كان الطلب معلقاً
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * التحقق مما إذا كان الطلب مقبولاً
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * التحقق مما إذا كان الطلب مرفوضاً
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * نطاق الاستعلام للطلبات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * نطاق الاستعلام للطلبات المقبولة
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * نطاق الاستعلام للطلبات المرفوضة
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * نطاق الاستعلام للطلبات حسب المستخدم
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * نطاق الاستعلام للطلبات حسب القسم
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * نطاق الاستعلام للطلبات في نطاق تاريخ معين
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q) use ($startDate, $endDate) {
                  $q->where('start_date', '<=', $startDate)
                    ->where('end_date', '>=', $endDate);
              });
        });
    }
}