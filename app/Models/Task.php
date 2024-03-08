<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = ['job_id', 'type'];

    protected $casts = ['result' => 'array'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function setProcessed(): void
    {
        $this->processed = true;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }
}
