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
                $data->shortDescription,
                $actprs,
            );

            array_push($tvGuide, $epg);
        }
        dd($tvGuide);
        return $tvGuide;
    }
}
