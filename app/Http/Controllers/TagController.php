<?php

namespace App\Http\Controllers;

use App\Models\FollowTag;
use Illuminate\Pagination\Paginator;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
class TagController extends Controller
{   
    public function search(Request $request)
    {
        $query = $request->input('query');
        // Use where() with the 'like' operator to search usernames containing the query string
        if(Auth::check()){
            $results = Tag::where('title','ILIKE',"%$query%" )->limit(10)->get();
            return response()->json($results);
        } else {
            return response()->json([
                'message' => 'Not logged in',
            ], 302);
        }
    }
    public function createform(Request $request){
        if(!Auth::check()){
            return redirect('/login');
        }
        if(Auth::user()->usertype !== 'admin'){
            return redirect('/home');
        }
        return view('pages.tagcreate');
    }
    public function create(Request $request)
    {
        if(!Auth::check()){
            return redirect('/login');
        }
        if(Auth::user()->usertype !== 'admin'){
            return redirect('/home');
        }
        $request->validate([
            'description' => 'required|string|min:8|max:100',
            'title' => 'required|string|min:2|max:30',
        ]);
        $this->authorize('create', Tag::class);
        $tag = new Tag();
        $tag->title = $request->input('title');
        $tag->description = $request->input('description');
        $tag->save();
        return redirect()->route('tagquestions', ['id' => $tag->id]);
    }
    public function editform(Request $request, $id){
        if(!Auth::check()){
            return redirect('/login');
        }
        if(Auth::user()->usertype !== 'admin'){
            return redirect('/home');
        }
        $tag = Tag::find($id);
        if($tag === null){
            return redirect()->route('tags')->withErrors(['tag' => 'The provided tag does not exist.']);
        }
        return view('pages.tagedit', ['tag' => $tag]);
    }
    public function edit(Request $request,$id)
    {
        if(!Auth::check()){
            return redirect('/login');
        }
        if(Auth::user()->usertype !== 'admin'){
            return redirect('/home');
        }
        $tag = Tag::find($id);
        $request->validate(['title' => 'required|string|min:3|max:80',
        'description' => 'required|string|min:8|max:255']);
        if($tag->title !== $request->input('title')){
            $request->validate(['title' => 'unique:tag',]);
        }
        $tag->title = $request->input('title');
        $tag->description = $request->input('description');
        $tag->save();
        return redirect()->route('tagquestions', ['id' => $tag->id]);
    }
    public function delete(Request $request, $id)
    {
        if(!Auth::check()){
            return redirect('/login');
        }
        if(Auth::user()->usertype !== 'admin'){
            return redirect('/home');
        }
        $this->authorize('delete', Tag::class);
        $tag = Tag::find($id);
        if($tag === null){
            return redirect()->route('tags')->withErrors(['tag' => 'The provided tag does not exist.']);
        }
        $result= TransactionsController::deleteTag($tag);
        if($result !== true){
            return redirect()->route('tags')->withErrors(['tag' => "something went wrong when deleting the tag: $tag->title"]);
        }
        return redirect()->route('tags')->withSuccess('You have deleted a tag!');
    }
    public function getTagsOfQuestion(Request $request, $id )
    {
        if(Auth::check()){
            $results = Tag::join('questiontag', 'tag.id', '=', 'questiontag.tag_id')
            ->Where('questiontag.question_id', '=', $id)
            ->get();//fix query to not return tags already in question

            return response()->json( $results);
        }else{
            return response()->json([
                'message' => 'Not logged in',
            ], 302);
        }
    }
    public function searchWithoutLimits(Request $request){
        $query = $request->input('query');
        if (Auth::check()) {
            if($query === null){
                $results = Tag::leftjoin('followtag', function ($join) {
                    $join->on('followtag.tag_id', '=', 'tag.id')
                         ->where('followtag.user_id', '=', Auth::user()->id);
                        })->Paginate(15)->withqueryString();
                foreach($results as $result){
                    if($result->user_id === null){
                        $result->followed = false;
                    }else{
                        $result->followed = true;
                    }
                }
                return response()->json($results);
            }
            $results = Tag::whereFullText('title',$query)
            ->orWhereFullText('description',$query)
            ->orderByRaw("ts_rank(tsvectors, plainto_tsquery(?)) DESC", [$query])
            ->leftjoin('followtag', function ($join) {
                $join->on('followtag.tag_id', '=', 'tag.id')
                     ->where('followtag.user_id', '=', Auth::user()->id);
                    })
            ->paginate(15)
            ->withQueryString();
            foreach($results as $result){
                if($result->user_id === null){
                    $result->followed = false;
                }else{
                    $result->followed = true;
                }
            }
            return response()->json($results);
        } else {
            return response()->json([
                'message' => 'Not logged in',
            ], 302);
        }
    }
    public function tagspage(){
        if(Auth::check()){
            return view("pages.tagsearch",['user_type' =>Auth::user()->usertype ]);
        } else {
            return redirect('/login');
        }

    }
    public function tagquestionspage($id){
        $tag =Tag::find($id);
        if(Auth::check() && $tag !== null){
            return view("pages.tagquestionsearch",['tag_id' => $id, 'tag_title' => $tag->title]);
        } else {
            return redirect('/login');
        }

    }
    public function tagquestions(Request $request, $tag_id){
        // Implement your search logic here
        if (! auth::check()){
            response()->json([
                'message' => 'Not logged in',
            ], 302);
        }
        $query = $request->input('q');
        $sortby = $request->input('OrderBy');
        if($query === null){
            if($sortby === 'relevance'){
                $sortby = 'date';
            }
            $results = Question::select(
                'question.title',
                'question.correct_answer_id',  
                'content.content', 
                'appuser.username', 
                'content.date', 
                'content.id as id', 
                'appuser.id as userid', 
                'content.votes as votes',
                'tags_agg.title as tags',
                'tags_agg.id as tagsid',
                DB::raw('COUNT(answer.id) as answernum')
            )
            ->join('content', 'question.id', '=', 'content.id')
            ->join('appuser', 'content.user_id', '=', 'appuser.id')
            ->join('questiontag', 'questiontag.question_id', '=', 'question.id')
            ->leftjoin('answer', 'answer.question_id', '=', 'question.id')
            ->leftjoin(
                DB::raw('(SELECT question.id as qid, STRING_AGG(tag.title, \',\' ORDER BY tag.id ASC) as title, STRING_AGG(CAST(tag.id AS TEXT), \',\' ORDER BY tag.id ASC) as id FROM questiontag JOIN tag ON tag.id = questiontag.tag_id JOIN question ON question.id = questiontag.question_id GROUP BY question.id) as tags_agg'),
                'tags_agg.qid',
                '=',
                'question.id'
            )
            ->where('questiontag.tag_id', '=', $tag_id)
            ->where('content.deleted', '=', false)
            ->groupBy(
                'question.title',
                'question.correct_answer_id',  
                'content.content',
                'appuser.username',
                'content.date',
                'content.id',
                'appuser.id',
                'content.votes',
                'tags_agg.title',
                'tags_agg.id'
            )
            ->orderBy($sortby, 'desc')
            ->paginate(15)->withQueryString()->withQueryString();

            foreach($results as $result){
                $result->date = $result->commentable->content->compileddate();
            }
        return response()->json($results);
        }
        if($sortby === 'relevance'){
            $results = Question::select(
                'question.title',
                'question.correct_answer_id',   
                'content.content', 
                'appuser.username', 
                'content.date', 
                'content.id as id', 
                'appuser.id as userid', 
                'content.votes as votes',
                'tags_agg.title as tags',
                'tags_agg.id as tagsid',
                DB::raw('COUNT(answer.id) as answernum')
            )
            ->join('content', 'question.id', '=', 'content.id')
            ->join('appuser', 'content.user_id', '=', 'appuser.id')
            ->join('questiontag', 'questiontag.question_id', '=', 'question.id')
            ->leftjoin('answer', 'answer.question_id', '=', 'question.id')
            ->leftjoin(
                DB::raw('(SELECT question.id as qid, STRING_AGG(tag.title, \',\' ORDER BY tag.id ASC) as title, STRING_AGG(CAST(tag.id AS TEXT), \',\' ORDER BY tag.id ASC) as id FROM questiontag JOIN tag ON tag.id = questiontag.tag_id JOIN question ON question.id = questiontag.question_id GROUP BY question.id) as tags_agg'),
                'tags_agg.qid',
                '=',
                'question.id'
            )
            ->where('questiontag.tag_id', '=', $tag_id)
            ->whereFullText('question.title',$query)
            ->where('content.deleted', '=', false)
            ->groupBy(
                'question.tsvectors',
                'question.title',
                'question.correct_answer_id',  
                'content.content',
                'appuser.username',
                'content.date',
                'content.id',
                'appuser.id',
                'content.votes',
                'tags_agg.title',
                'tags_agg.id'
            )
            ->orderByRaw("ts_rank(question.tsvectors, plainto_tsquery(?)) ASC", [$query])
            ->paginate(15)->withQueryString()->withQueryString();

                foreach($results as $result){
                    $result->date = $result->commentable->content->compileddate();
                }
            return response()->json($results);
        }else{
            $results = Question::select(
                'question.title',
                'question.correct_answer_id',   
                'content.content', 
                'appuser.username', 
                'content.date', 
                'content.id as id', 
                'appuser.id as userid', 
                'content.votes as votes',
                'tags_agg.title as tags',
                'tags_agg.id as tagsid',
                DB::raw('COUNT(answer.id) as answernum')
            )
            ->join('content', 'question.id', '=', 'content.id')
            ->join('appuser', 'content.user_id', '=', 'appuser.id')
            ->join('questiontag', 'questiontag.question_id', '=', 'question.id')
            ->leftjoin('answer', 'answer.question_id', '=', 'question.id')
            ->leftjoin(
                DB::raw('(SELECT question.id as qid, STRING_AGG(tag.title, \',\' ORDER BY tag.id ASC) as title, STRING_AGG(CAST(tag.id AS TEXT), \',\' ORDER BY tag.id ASC) as id FROM questiontag JOIN tag ON tag.id = questiontag.tag_id JOIN question ON question.id = questiontag.question_id GROUP BY question.id) as tags_agg'),
                'tags_agg.qid',
                '=',
                'question.id'
            )
            ->where('questiontag.tag_id', '=', $tag_id)
            ->whereFullText('question.title',$query)
            ->where('content.deleted', '=', false)
            ->groupBy(
                'question.title',
                'question.tsvectors',
                'question.correct_answer_id',  
                'content.content',
                'appuser.username',
                'content.date',
                'content.id',
                'appuser.id',
                'content.votes',
                'tags_agg.title',
                'tags_agg.id'
            )
            ->orderBy($sortby, 'desc')
            ->orderByRaw("ts_rank(question.tsvectors, plainto_tsquery(?)) ASC", [$query])
            ->paginate(10)->withQueryString()->withQueryString();

                foreach($results as $result){
                    $result->date = $result->commentable->content->compileddate();
                }
            return response()->json($results);
        }
    }
    public function follow($id){
        if(!Auth::check()){
            return redirect('/login');
        }
        $tag = Tag::find($id);
        if($tag === null){
            return redirect()->route('tags')->withErrors(['tag' => 'The provided tag does not exist.']);
        }
        $followTag = FollowTag::where('user_id', Auth::user()->id)->where('tag_id', $id)->first();
        if($followTag !== null){
            $followTag->delete();
        }else{
            $newfollowTag = new FollowTag([
                'user_id' => Auth::user()->id,
                'tag_id' => $id
            ]);
            $newfollowTag->save();
        }

        return redirect()->route('tagquestions', ['id' => $tag->id]);
    }
}