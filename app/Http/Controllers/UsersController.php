<?php

namespace App\Http\Controllers;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{   
    public function search(Request $request)
    {
    $query = $request->input('q');
    $searchBy = $request->input('SearchBy');
    // If the query is empty, return all users
    if(Auth::check()){
            // Use where() with the 'like' operator to search usernames containing the query string
            if($query === null){
                $results = User::where('name','<>','Deleted')->paginate(15)->withQueryString();
                return response()->json($results);
            }
            if($searchBy === 'relevance'){
                $results = User::whereFullText('username',$query)->orWhereFullText('name',$query)
                ->orderByRaw("ts_rank(tsvectors, plainto_tsquery(?)) DESC", [$query])
                ->where('name','<>','Deleted')->paginate(15)->withQueryString()->withQueryString();
                return response()->json($results);
            }
            $results = User::whereFullText($searchBy,$query)
            ->orderByRaw("ts_rank(tsvectors, plainto_tsquery(?)) DESC", [$query])
            ->where('name','<>','Deleted')->paginate(15)->withQueryString()->withQueryString();
            return response()->json($results);
    }else{
        return response()->json([
            'message' => 'Not logged in',
        ], 302);
    }
    }
 
    public function getUsers(){
        if( Auth::check()){
            $users = User::all();
            return view('pages.users', ['users' => $users]);
        } else {
            return redirect('/login');
        }

    }
    
}