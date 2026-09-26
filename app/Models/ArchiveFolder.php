<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveFolder extends Model
{
    protected $fillable = [
        'folder_id',
        'owner',
        'name',
        'permision'
    ];
}
