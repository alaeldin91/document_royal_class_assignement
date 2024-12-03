<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

      protected $table = 'audit_logs';

      protected $fillable = [
          'user_id',
          'document_id',
          'action',
          'changes_summary',
          'timestamp',
          'ip_address',
          'user_agent',
      ];
  
      protected $casts = [
          'changes_summary' => 'array',
          'timestamp' => 'datetime',
      ];
  
      public function user()
      {
          return $this->belongsTo(User::class);
      }
  
      public function document()
      {
          return $this->belongsTo(DocumentHeader::class);
      }

}