<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactCustomFeildMaster extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'label',
        'slug',
        'field_id',
        'is_required',
        'default_value'
    ];

    public function field():BelongsTo{
        return $this->belongsTo(CustomFieldMaster::class, 'field_id', 'id');
    }
}
