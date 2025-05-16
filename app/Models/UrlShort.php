<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlShort extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'short',
        'original',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    protected $attributes = [];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function setCreatedAtAttribute($value)
    {
        // to Disable created_at
    }

    public static function make(string $url): string
    {
        $key = self::generateKey(self::count(), function ($key) {
            return self::where('key', $key)->exists();
        });

        $shortUrl = env('APP_URL') . '/' . env('APP_PREFIX_FOLDER') . '/s/' . $key;
        $UrlShort = new self();
        $UrlShort->key = $key;
        $UrlShort->short = $shortUrl;
        $UrlShort->original_url = $url;
        $UrlShort->save();

        return $UrlShort->short;
    }

    private static function generateKey(int $existingCount, callable $isKeyTaken): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $base = strlen($chars);
        $length = 3;

        // Calcula o comprimento mínimo necessário com base no número de registros
        while (pow($base, $length) <= $existingCount) {
            $length++;
        }

        do {
            $key = '';
            for ($i = 0; $i < $length; $i++) {
                $key .= $chars[random_int(0, $base - 1)];
            }
        } while ($isKeyTaken($key)); // verifica se já existe na base

        return $key;
    }
}
