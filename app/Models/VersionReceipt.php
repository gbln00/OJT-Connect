<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VersionReadReceipt extends Model
{
    protected $connection = 'mysql';
    protected $fillable = ['version_id', 'tenant_id', 'read_by', 'read_at'];
    protected $casts = ['read_at' => 'datetime'];

    public function version()
    {
        return $this->belongsTo(SystemVersion::class, 'version_id');
    }
}