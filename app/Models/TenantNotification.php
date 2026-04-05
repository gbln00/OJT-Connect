<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantNotification extends Model
{
    
    protected $fillable = ['type', 'title', 'message', 'is_read'];

    protected $casts = ['is_read' => 'boolean'];

    public function scopeUnread($query) {
        return $query->where('is_read', false);
    }

    public static function notify(string $title, string $message, string $type = 'info'): self {
        return self::create(compact('title', 'message', 'type'));
    }
}