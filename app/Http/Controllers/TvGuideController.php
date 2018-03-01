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

    public function GetDataFromEPG($station, $date, $bu)
    {
        $curl = $this->InitCurlTvShowsGuide($station, $date, $bu);

        $data = json_decode($curl);

        $tvGuideData = array();

        foreach($data as $item) {
            $actors = array();

            foreach($item->actors as $actor) {
                array_push($actors, $actor->realName);
            }

            $schedule = array();

            array_push($schedule, $item->start->date);
            array_push($schedule, $item->end->date);


            $tempTvGuideData = array(
                'title' => $item->title,
                'startDate' => $schedule[0],
                'endDate' => $schedule[1],
                'actors' => $actors,
            );

            array_push($tvGuideData, $tempTvGuideData);
        }
        $tvGuideData = json_encode($tvGuideData);
        return $tvGuideData;
    }

    public function GetInfoByTMDb($title){

    }
}
