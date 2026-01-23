<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitabChapter extends Model
{
    use HasFactory;

    protected $table = "kitab_chapters";

    protected $fillable = ["kitab_id", "nomor_bab", "judul_bab", "deskripsi", "slug", "urutan"];

    /**
     * Relasi ke kitab parent
     */
    public function kitab()
    {
        return $this->belongsTo(Kitab::class, 'kitab_id');
    }

    /**
     * Relasi ke maqolah-maqolah dalam bab ini
     */
    public function maqolahs()
    {
        return $this->hasMany(KitabMaqolah::class, "chapter_id")->orderBy("urutan");
    }

    /**
     * Hitung jumlah maqolah dalam bab
     */
    public function getMaqolahsCountAttribute()
    {
        return $this->maqolahs()->count();
    }
}
