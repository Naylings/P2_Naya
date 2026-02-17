<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LurahConfig extends Model
{
    protected $table = 'lurah_config';

    protected $fillable = [
        'name',
        'province',
        'city',
        'district',
        'pos_code',
        'logo',
    ];

    /**
     * Ambil config (selalu 1 row).
     * Jika belum ada, return null.
     */
    public static function getConfig(): ?self
    {
        return self::first();
    }

    /**
     * Simpan config — update jika sudah ada, create jika belum.
     */
    public static function saveConfig(array $data): self
    {
        $config = self::first();

        if ($config) {
            $config->update($data);
            return $config->fresh();
        }

        return self::create($data);
    }
}