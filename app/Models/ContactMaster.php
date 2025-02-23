<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMaster extends Model
{
    //

    use SoftDeletes;

    protected $table = 'contact_masters';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'profile_photo',
        'gender_id',
        'is_merged',
        'parent_contact_id'
    ];

    protected $casts = [
        'is_merged'=>'boolean'
    ];

    public function gender():BelongsTo{
        return $this->belongsTo(GenderMaster::class, 'gender_id', 'id');
    }
}
