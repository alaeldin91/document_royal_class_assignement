<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class DocumentHeader extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
   use Searchable;
    protected $fillable = ['user_id', 'module', 'version', 'metadata', 'encryption_key','created_at','updated_at'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function bodies()
    {
        return $this->hasMany(DocumentBody::class, 'document_id');
    }

    public function versions()
    {
        return $this->hasMany(Version::class, 'document_id');
    }

    public function decryptBody($encryptedBody, $encryptionKey, $iv)
    {
        $decrypted = openssl_decrypt(
            base64_decode($encryptedBody),
            'aes-256-cbc', 
            $encryptionKey,
            0,
            $iv 
        );

        return $decrypted;
    }

    public function toSearchableArray()
    {
        return [
            'user_id' => $this->user_id,
            'module' => $this->module,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
