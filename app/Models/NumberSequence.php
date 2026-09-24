<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NumberSequence extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prefix',
        'period',
        'last_number',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_number' => 'integer',
        ];
    }

    /**
     * Generate the next formatted ticket number with atomic row-level lock.
     * Format: {prefix}-{period}-{padded_number} (e.g. DTSEN-202610-00001).
     */
    public static function generateNext(string $prefix, ?string $period = null, int $padding = 5): string
    {
        $period = $period ?? Carbon::now()->format('Ym');

        return DB::transaction(function () use ($prefix, $period, $padding): string {
            $sequence = static::where('prefix', $prefix)
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                $sequence = static::create([
                    'prefix' => $prefix,
                    'period' => $period,
                    'last_number' => 0,
                ]);
            }

            $sequence->increment('last_number');
            $padded = str_pad((string) $sequence->last_number, $padding, '0', STR_PAD_LEFT);

            return "{$prefix}-{$period}-{$padded}";
        });
    }
}
