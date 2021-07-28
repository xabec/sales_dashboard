<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Import
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Import query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $filename
 * @property string $filepath
 * @property int $success
 * @property int $failures
 * @property int $duplicate
 * @property int $complete
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereDuplicate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereFailures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereFilepath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereUpdatedAt($value)
 */
class Import extends Model
{
    use HasFactory;

    public $fillable = ['filename', 'filepath'];
    public $casts = [
        'complete' => 'boolean',
        'failures_list' => 'array'
    ];

}
