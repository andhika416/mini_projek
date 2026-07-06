<?php

namespace App\Models;

use Database\Factories\WorkReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkReport extends Model
{
    /** @use HasFactory<WorkReportFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'input_date', 'latitude', 'longitude', 'start_time',
        'end_time', 'work_plan', 'work_activity', 'work_result',
        'attachment_path', 'attachment_name',
    ];

    protected function casts(): array
    {
        return [
            'input_date' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
