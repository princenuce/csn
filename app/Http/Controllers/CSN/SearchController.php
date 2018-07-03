<?php

namespace App\Http\Controllers\CSN;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Song;
use Illuminate\Http\Request;

class SearchController extends Controller {
    private const SEARCH_URL = 'http://search.chiasenhac.vn/search.php?cat=music&s=';
    private $query;

    public function search(Request $req) {
        $this->set_query($req->route('search_query'));

        return view(
            Helper::template() . '.search',
            [
                'query' => $this->query,
                'data'  => $this->get_search_results(),
            ]
        );
    }

    /**
     * Set query
     * @param String $query
     * Return void
     */
    private function set_query($query) {
        $this->query = $query;
    }

    private function get_search_results() {
        $url  = self::SEARCH_URL . urlencode($this->query);
        $html = Helper::curl_shell($url);

        return $this->filter_data($html);
    }

    private function filter_data($html) {
        $infos     = $this->filter_infos($html);
        $downloads = $this->filter_downloads($html);
        $durations = $this->filter_durations($html);
        dd($infos, $downloads, $durations);
        $songs          = [];
        $unique_id_list = [];
        foreach ($infos as $key => $info) {
            preg_match('#[^~]+?(?=\.html)#', $info[0], $unique_id);

            if (!isset($unique_id[0])) {
                continue;
            }
            // ID CSN
            $unique_id = $unique_id[0];

            // danh sach ID bai hat se duoc dung de query lay ra cac bai hat da insert
            $unique_id_list[] = $unique_id;

            $data[$unique_id] = [
                'unique_id' => $unique_id,
                'name'      => $info[1],
                'slug'      => Helper::nonuni($info[1]),
                'listen'    => $downloads[$key],
                'duration'  => $durations[$key],
                'single'    => $info[2],
                'url'       => $info[0],
            ];
        }

        $get = ['unique_id', 'id', 'name', 'slug', 'listen', 'duration', 'single'];

        // lấy ra tất cả bài hát theo unique_id đã được thêm
        $songs = Song::whereIn('unique_id', $unique_id_list)->get($get)->toArray();

        // sắp xếp lại mảng
        $songs_inserted = array_column($songs, 'id', 'unique_id');

        // lọc ra các bài hát chưa được lưu
        $songs_not_inserted = array_diff_key($data, $songs_inserted);

        // thêm các bài hát chưa lưu
        $inserted = [];
        if (empty($songs_not_inserted)) {
            return $songs;
        }

        if (!Song::insert($songs_not_inserted)) {
            throw new SongException("Có lỗi không xác định", "");
        } else {
            $inserted = Song::whereIn('unique_id', $unique_id_list)->get($get)->toArray();
        }

        return array_merge($songs, $inserted);
    }

    private function filter_infos($html) {
        $infos = [];
        $regex = '#(?<=<div class="tenbh">)[\s\r\n]*<p><a href="(.+?)".*>(.+?)</a></p>[\s\r\n]*<p>(.+?)</p>#';
        preg_replace_callback($regex, function ($matches) use (&$infos) {
            $infos[] = array_splice($matches, 1);
        }, $html);

        return $infos;
    }

    private function filter_downloads($html) {
        $downloads = [];
        $regex     = '#(?<=<td nowrap="nowrap" align="center">)[\s\r\n]*<p>([\d.]+)#';
        preg_replace_callback($regex, function ($matches) use (&$downloads) {
            $downloads[] = str_replace(['.', ''], ['', ''], $matches[1]);
        }, $html);

        return $downloads;
    }

    private function filter_durations($html) {
        $durations = [];
        $regex     = '#(?<=<td nowrap="nowrap" align="center">)[\s\r\n]*<span class="gen">([\d:]+)#';
        preg_replace_callback($regex, function ($matches) use (&$durations) {
            $matches     = trim(strip_tags($matches[1]));
            $array       = explode(':', $matches);
            $array       = array_reverse($array);
            $second      = $array[0] + $array[1] * 60 + (isset($array[2]) ? $array[2] * 3600 : 0);
            $durations[] = $second;
        }, $html);

        return $durations;
    }
}
