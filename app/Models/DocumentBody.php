<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class DocumentBody extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;
    protected $dates = ['deleted_at'];
    protected $fillable = ['document_id', 'content', 'checksum'];

    public function documentHeader()
    {
        return $this->belongsTo(DocumentHeader::class, 'document_id');
    }
}

