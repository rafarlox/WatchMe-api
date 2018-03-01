<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TvGuideController extends Controller
{


    /**
     * Use the API of the rts : https://developer.srgssr.ch/apis/srgssr-epg/tvshowsguide
     * @param $station
     * @param $date
     * @param $bu
     * @return mixed
     */
    public function InitCurlTvShowsGuide($station, $date, $bu) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.srgssr.ch/epg/v2/tvshows/stations/' . $station . '?date=' . $date . '&bu=' . $bu,
            CURLOPT_HTTPHEADER => array('accept: application/json', 'Authorization:  ', 'Authorization: Bearer H7WjusPyormcKPVGPU06DgC0VgdV')
        ));

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    public function GetGuideTv($station, $date, $bu)
    {
        $curl = $this->InitCurlTvShowsGuide($station, $date, $bu);

        $datas = json_decode($curl);

        $tvGuide = array();

        foreach($datas as $data) {
            $actors = array();

            foreach($data->actors as $actor) {
                array_push($actors, $actor->realName);
            }

            $epg = array(
                $data->title,
                $actors
            );

            array_push($tvGuide, $epg);
        }
        dd($tvGuide);
        return $tvGuide;
    }

    private function InitTMDb(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.themoviedb.org/3/authentication/token/new?api_key=1e04de70b2b99214c95b0e9cd9bf9b9b",
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "{}",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }

    public function GetInfoByTMDb($title){
        $curl = curl_init();

        $result = array();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.themoviedb.org/3/search/movie?api_key=1e04de70b2b99214c95b0e9cd9bf9b9b&query=" . str_replace(' ', '%20', $title),
            CURLOPT_RETURNTRANSFER => 1
        ));

        $response = curl_exec($curl);

        $data = json_decode($response);

        //dd($data->results);

        foreach ($data->results as $key => $value) {

            $movie = array($value->title, $value->original_title, $value->release_date, $value->genre_ids);

            array_push($result, $movie);
        }

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            dd($result);
            echo $result;
        }
    }
}
