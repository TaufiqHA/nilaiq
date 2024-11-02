<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $guarded = ['id'];

    public function isActive() {
        return Carbon::now()->lessThanOrEqualTo($this->tahun_selesai);
    }
}
