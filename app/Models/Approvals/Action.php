<?php

namespace App\Models\Approvals;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Structure
{
    use HasFactory;

    protected $table = 'approval_actions';
    protected $primaryKey = 'id';
    
    // Keep columns minimal and explicit for morph relation
    protected $fillable = [
        'decision',            // e.g. approved, rejected, forwarded
        'description',
        'reference_type',   // polymorphic type (Invoice, PurchaseOrder...)
        'reference_id',     // polymorphic id
        'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at',
    ];

    // Common action constants (customize to your app)
    public const ACTION_APPROVED  = 'approved';
    public const ACTION_REJECTED  = 'rejected';
    public const ACTION_FORWARDED = 'forwarded';
    public const ACTION_COMMENTED = 'commented';

    /**
     * Polymorphic relation to the target being approved (Invoice, PurchaseOrder, ...)
     *
     * Use as: $action->approvable (returns the related model)
     */
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * User that created the action
     */
    public function creater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    /**
     * User that last updated the action
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'id');
    }

    /**
     * User that deleted (voided) the action
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by', 'id');
    }

    /**
     * Scope to filter actions for a specific approvable model type (string)
     * Example: Action::forType(\App\Models\Finance\Invoice::class)->get();
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('approvable_type', $type);
    }

    /**
     * Scope to filter actions for a specific model instance
     * Example: Action::forModel($invoice)->get();
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('approvable_type', get_class($model))
                     ->where('approvable_id', $model->getKey());
    }

    /**
     * Create a friendly description helper (optional)
     */
    public function summary(): string
    {
        return sprintf(
            '%s: %s (by %s)',
            $this->action,
            $this->description ?: '-',
            optional($this->creater)->name ?: 'system'
        );
    }

    /**
     * Optional booted handler to automatically fill created_by/updated_by if you want.
     * NOTE: this uses auth() — remove or adapt if you don't want this implicit behavior.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check() && empty($model->created_by)) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}