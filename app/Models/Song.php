<?php

namespace App\Models;

use App\Models\Song;
use Illuminate\Database\Eloquent\Model;

class Song extends Model {
    public $primaryKey = 'id';

    protected $fillable = ['unique_id', 'name', 'slug', 'listen', 'duration', 'single', 'url'];

    public function related() {
        $this->related = Song::join('relations', 'songs.unique_id', '=', 'relations.belong_to_id')
            ->where('relations.id', $this->unique_id)
            ->get(['songs.id', 'songs.name', 'songs.slug', 'songs.single', 'songs.id', 'songs.duration', 'songs.listen'])
            ->toArray();
    }
}
