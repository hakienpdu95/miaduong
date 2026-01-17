<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Constants\ModuleConst;

class Permission extends Model
{
    protected $fillable = [ 'user_id', 'module_name' ];
    
    protected $casts = ['expired_at' => 'datetime'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Gán động fillable và casts
        $this->fillable = array_merge(
            $this->fillable,
            array_map(fn($action) => "can_{$action}", ModuleConst::getActions() ?? [])
        );

        $this->casts = array_merge(
            $this->casts,
            array_fill_keys(
                array_map(fn($action) => "can_{$action}", ModuleConst::getActions() ?? []),
                'boolean'
            )
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}