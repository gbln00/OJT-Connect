<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class TenantSetting extends Model
{
    protected $fillable = ['key', 'value'];
 
    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }
 
    /**
     * Set (upsert) a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
 
    /**
     * Return all settings as a key => value array.
     */
    public static function allAsArray(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }
}
