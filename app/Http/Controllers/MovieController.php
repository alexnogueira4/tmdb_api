<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class MovieController extends Controller
{
    public function httpRequest($url = '', $page = null)
    {
      // $API_KEY = env('TMDB_KEY');
      $API_KEY = '1f54bd990f1cdfb230adb312546d765d';

      /* Define data language on requisition */
      $lang_param = Input::get('language');
      $lang = $lang_param ? $lang_param : 'pt-BR';

      /* Make http request to TMDB API and return a JSON response */
      $client = new Client();

      try {
        $response = $client->request('GET', 'https://api.themoviedb.org/3/' . $url . '?&language=' . $lang . '&api_key=' . $API_KEY . $page);
        return json_decode($response->getBody());
      } catch (RequestException $e) {
        return array("results" => []);
      }

    }

    public function showMovies()
    {
        $page = Input::get('page');
        $page = $page ? '&page=' . $page : null;

        $type = Input::get('type');
        $url = 'movie/' . ($type ? $type : null);

        $movies = $this->httpRequest($url, $page);

        $all_genres = $this->getGenres();

        foreach ($movies->results as $movie) {
          $movie->genres = array();
          foreach ($movie->genre_ids as $genreId) {
            foreach ($all_genres->genres as $genre) {
              if ($genreId === $genre->id) {
                array_push($movie->genres, $genre->name);
              }
            }
          }
        }

        return response()->json($movies);
    }

    public function showOneMovie($id)
    {
        $url = 'movie/' . $id;
        $movies = $this->httpRequest($url);
        return response()->json($movies);
    }

    public function searchMovies()
    {
      $page = Input::get('page');
      $query = Input::get('query');
      $page = $page ? '&page=' . $page : null;
      $url = 'search/movie?query=' . $query;

      $movies = $this->httpRequest($url, $page);

      $all_genres = $this->getGenres();

      foreach ($movies->results as $movie) {
        $movie->genres = array();
        foreach ($movie->genre_ids as $genreId) {
          foreach ($all_genres->genres as $genre) {
            if ($genreId === $genre->id) {
              array_push($movie->genres, $genre->name);
            }
          }
        }
      }

      return response()->json($movies);
    }

    public function getGenres()
    {
      $url = 'genre/movie/list';
      $all_genres = $this->httpRequest($url);
      return $all_genres;
    }
}
