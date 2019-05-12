<?php

namespace App\Http\Controllers;

// use App\Author;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;


class AuthorController extends Controller
{
    // const API_ENDPOINT = 'https://api.themoviedb.org/3/movie/76341';
    public function httpRequest($url = '')
    {
      $API_KEY = env('TMDB_KEY');

      /* Define data language on requisition */
      $lang_param = Input::get('language');
      $lang = $lang_param ? $lang_param : 'pt-BR';

      /* Make http request to TMDB API and return a JSON response */
      $client = new Client();
      $response = '';

      try {
        $response = $client->request('GET', 'https://api.themoviedb.org/3/' . $url . '?&language=' . $lang . '&api_key=' . $API_KEY);
      } catch (RequestException $e) {
        return array("results" => []);
      }

      return json_decode($response->getBody());
    }

    public function showMovies()
    {
        $url = 'movie/upcoming';
        $movies = $this->httpRequest($url);
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

    public function getGenres()
    {
      $url = 'genre/movie/list';
      $all_genres = $this->httpRequest($url);
      return $all_genres;
    }

    // public function create(Request $request)
    // {
    //     $author = Author::create($request->all());
    //
    //     return response()->json($author, 201);
    // }
    //
    // public function update($id, Request $request)
    // {
    //     $author = Author::findOrFail($id);
    //     $author->update($request->all());
    //
    //     return response()->json($author, 200);
    // }
    //
    // public function delete($id)
    // {
    //     Author::findOrFail($id)->delete();
    //     return response('Deleted Successfully', 200);
    // }
}
