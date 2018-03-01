<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TvGuideController extends Controller
{

    public function InitCurlTvShowsGuide($station, $date, $bu) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.srgssr.ch/epg/v2/tvshows/stations/rts-1?date=2018-03-01&bu=SRF',
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

        foreach($datas as $data)
        {
            $epg = array(
                $data->title,
                $data->subTitle,
                $data->shortDescription,
                $data->actors
            );

            array_push($titles, $epg);
        }
        dd($tvGuide);

    }

    public function GetInfoByTMDb($title){

    }
}
