<?php

namespace App\Http\Controllers\CSN;

use App\Exceptions\SongException;
use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Relation;
use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller {
    private static $real_url  = null;
    private static $unique_id = null;
    private $id               = null;

    public function index(Request $req) {
        $this->id = $req->route('id');

        $song = Song::find($this->id);

        if (is_null($song)) {
            throw new SongException(
                'Bài hát không tồn tại',
                'Chúng tôi không tìm thấy bài hát này trên hệ thống'
            );
        }

        if (!isset($song->link_play, $song->url)) {
            $this->update_link_play($song);
        } else {
            $song->related();
        }

        return view(
            Helper::template() . '.song',
            [
                'song' => $song,
            ]
        );
    }

    private function update_link_play(Song $song) {
        self::$real_url  = $song->url;
        $song->unique_id = $this->filter_id();
        $song->url       = self::$real_url;

        $html = $this->curl_shell(self::$real_url);

        $matches      = $this->filter_song_name_author($html);
        $song->name   = $matches[0];
        $song->slug   = Helper::nonuni($song->name);
        $song->single = $matches[1];

        $matches         = $this->filter_song_mp3($html);
        $song->link_play = json_encode($matches);

        $matches     = $this->filter_lyric($html);
        $song->lyric = $matches;

        $song->save();
        $song->related = $this->filter_related($html);
    }

    private function save_tmp_song($related) {
        if (!count($related['urls'])) {
            return [];
        }

        foreach ($related['urls'] as $key => $url) {
            preg_match('#[^~]+?(?=\.html)#', $url, $unique_id);

            if (!isset($unique_id[0])) {
                continue;
            }

            // ID CSN
            $unique_id = $unique_id[0];

            // danh sach ID bai hat se duoc dung de query lay ra cac bai hat da insert
            $unique_id_list[] = $unique_id;

            // danh sach thong tin bai hat se duoc dung de insert vao database
            $rela_songs[$unique_id] = [
                'unique_id' => $unique_id,
                'name'      => $related['names'][$key],
                'slug'      => Helper::nonuni($related['names'][$key]),
                'single'    => $related['singles'][$key],
                'url'       => $url,
            ];

            // relation ship list
            $relation[] = [
                'id'           => self::$unique_id,
                'belong_to_id' => $unique_id,
            ];
        }

        // lấy ra tất cả bài hát theo unique_id đã được thêm
        $songs = Song::whereIn('unique_id', $unique_id_list)->get(['id', 'unique_id'])->toArray();

        // sắp xếp lại mảng
        $songs_inserted = array_column($songs, 'id', 'unique_id');

        // lọc ra các bài hát chưa được lưu
        $songs_not_inserted = array_diff_key($rela_songs, $songs_inserted);

        // thêm các bài hát chưa lưu
        if (!Song::insert($songs_not_inserted) || !Relation::insert($relation)) {
            throw new SongException("Có lỗi không xác định", "");
        }

        return Song::whereIn('unique_id', $unique_id_list)->get(['id', 'name', 'slug', 'single'])->toArray();
    }

    private function filter_id() {
        preg_match('#[^~]+?(?=\.html)#', self::$real_url, $matches);

        if (!isset($matches[0])) {
            throw new SongException(
                'Bài hát không hợp lệ',
                'Vì lý do nào đó mà bài hát này chúng tôi phát hiện rằng nó không hợp lệ'
            );
        }

        self::$unique_id = $matches[0];

        return self::$unique_id;
    }

    private function filter_song_mp3($html) {
        preg_match('#media:\s*\{\s*title:\s*".+?",\s*mp3:\s*.+?\("(.+?)",\s*"(.+?)",\s*"(.+?)", 1\)\s*\}#s', $html, $matches);

        return array_splice($matches, 1);
    }

    private function filter_song_name_author($html) {
        preg_match('#<meta name="title" content="(.*?) ~ (.*?)" />#', $html, $matches);

        return array_splice($matches, 1);
    }

    private function filter_lyric($html) {
        preg_match('#<p class="genmed".*?>(.+?)<\/p>#s', $html, $matches);

        return array_splice($matches, 1)[0];
    }

    /**
     * Lọc bài hát liên quan
     * @param  String $html Source html
     * @return Hàm lưu tạm bài hát
     */
    private function filter_related($html) {
        $regex = '#(?<=Bài hát liên quan)(.+?)</table>#s';
        preg_match($regex, $html, $matches);

        preg_match_all('#<a href="(.+?)".*class="musictitle".*>(.+?)</a>#', $matches[0], $a);
        preg_match_all('#<p class="gen">(.+)</p>#', $matches[0], $p);

        $infos = [
            'urls'    => $a[1],
            'names'   => $a[2],
            'singles' => $p[1],
        ];

        return $this->save_tmp_song($infos);
    }

    private function curl_shell($url) {
        $cmd = 'curl -Ls -m 3 "' . $url . '"';
        return shell_exec($cmd);
    }

    private function curl($url) {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($curl, CURLOPT_NOBODY, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        // curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        return $curl;
    }
}
