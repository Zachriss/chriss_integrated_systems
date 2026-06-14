<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = ['file_name', 'file_path', 'file_size', 'backup_type', 'created_by'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($backup) {
            if (empty($backup->backup_type)) {
                $backup->backup_type = 'sql';
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}