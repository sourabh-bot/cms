<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactAddtionInformaton extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'contact_id',
        'value',
        'custom_field_id'
    ];

    public function custom_field():BelongsTo{
        return $this->belongsTo(ContactCustomFeildMaster::class, 'custom_field_id', 'id');
    }
}
