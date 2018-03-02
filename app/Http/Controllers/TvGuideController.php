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

        return response()->json($tvGuideData);
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

    public function GetInfoMovieByTitle($title){
        $curl = curl_init();

        $result = array();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.themoviedb.org/3/search/movie?api_key=1e04de70b2b99214c95b0e9cd9bf9b9b&query=" . str_replace(' ', '%20', $title),
            CURLOPT_RETURNTRANSFER => 1
        ));

        $response = curl_exec($curl);

        $data = json_decode($response);

        foreach ($data->results as $key => $value) {

            $movie = array('id'=>$value->id, 'title'=>$value->title, 'release_date'=>$value->release_date, 'poster_path'=>$value->poster_path);

            array_push($result, $movie);
        }

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return response()->json($result);
        }
    }

    public function GetGenresByIdMovie($id)
    {
        $curl = curl_init();

        $result = array();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.themoviedb.org/3/movie/" . $id . "?api_key=1e04de70b2b99214c95b0e9cd9bf9b9b",
            CURLOPT_RETURNTRANSFER => 1
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response);

        //$genres = $data->genres;


        for ($i = 0; $i < count($data->genres); $i++) {
            array_push($result, $data->genres[$i]->name);
        }

        return $result;
    }

    /**
     * @param $idTitle
     * @param $page
     * @param $language (ex : en-US)
     * @return array|string
     */
    public function GetIdRecommendationsTitle($idTitle, $page = 1, $language = "en-US") {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.themoviedb.org/3/movie/" . $idTitle . "/recommendations?api_key=1e04de70b2b99214c95b0e9cd9bf9b9b&page=" . $page . "&language=" . $language,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "{}",
        ));

        $response = curl_exec($curl);
        $data = json_decode($response);

        $idRecommendationsTitle = array();

        foreach($data->results as $item) {

            $arrayTemp = array(
                'id' => $item->id,
            );
            array_push($idRecommendationsTitle, $arrayTemp);
        }

        curl_close($curl);
        return response()->json($idRecommendationsTitle);
    }

    public function SearchActor($query, $adult = false, $page = 1, $language = "en-US") {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.themoviedb.org/3/search/person?include_adult=" . $adult . "&page=" . $page . "&query=" . str_replace(' ', '%20', $query) . "&language=" . $language . "&api_key=1e04de70b2b99214c95b0e9cd9bf9b9b",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "{}",
        ));

        $response = curl_exec($curl);
        $data = json_decode($response);

        $actors = array();


        foreach($data->results as $item) {
            
            $moviesKnownForActor = array();

            foreach($item->known_for as $movie) {

                if(isset($movie->title))
                    array_push($moviesKnownForActor, $movie->title, $movie->genre_ids, $movie->poster_path);
            }

            $arrayTemp = array(
                'name' => $item->name,
                'know_for' => $moviesKnownForActor,


            );

            array_push($actors, $arrayTemp);
        }
        curl_close($curl);
        return response()->json($actors);
    }

    public function GetAllDetailsByIDMovie($id){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.themoviedb.org/3/movie/" . $id . "?api_key=1e04de70b2b99214c95b0e9cd9bf9b9b",
            CURLOPT_RETURNTRANSFER => 1
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response);

        $genres = array();

        for ($i = 0; $i < count($data->genres); $i++) {
            array_push($genres, $data->genres[$i]->name);
        }

        $movie = array('id'=>$data->id, 'title'=>$data->title, 'original_title'=>$data->original_title, 'release_date'=>$data->release_date, 'poster_path'=>$data->poster_path, 'genres'=>$genres);

        return response()->json($movie);
    }

    public function GetMoviesByGenres($genres){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.themoviedb.org/3/discover/movie?api_key=1e04de70b2b99214c95b0e9cd9bf9b9b&with_genres=" . str_replace(' ', '%20', $genres),
            CURLOPT_RETURNTRANSFER => 1
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response);

        $movie = array($data->id, $data->title, $data->original_title, $data->release_date);

        dd(movie);

        return json_encode($movie);
    }


    public function GetInfoSeriesByTitle($title){
        $curl = curl_init();

        $result = array();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.themoviedb.org/3/search/tv?api_key=1e04de70b2b99214c95b0e9cd9bf9b9b&query=" . str_replace(' ', '%20', $title),
            CURLOPT_RETURNTRANSFER => 1
        ));

        $response = curl_exec($curl);

        $data = json_decode($response);

        foreach ($data->results as $key => $value) {

            $movie = array('id'=>$value->id, 'title'=>$value->name, 'release_date'=>$value->first_air_date, 'poster_path'=>$value->poster_path);

            array_push($result, $movie);
        }

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return response()->json($result);
        }
    }


    public function GetAllDetailsByIDSeries($id){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.themoviedb.org/3/tv/" . $id . "?api_key=1e04de70b2b99214c95b0e9cd9bf9b9b",
            CURLOPT_RETURNTRANSFER => 1
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response);

        $genres = array();

        for ($i = 0; $i < count($data->genres); $i++) {
            array_push($genres, $data->genres[$i]->name);
        }

        $movie = array('id'=>$data->id, 'title'=>$data->name, 'original_title'=>$data->original_name, 'release_date'=>$data->first_air_date, 'poster_path'=>$data->poster_path, 'genres'=>$genres);

        return response()->json($movie);
    }
}

