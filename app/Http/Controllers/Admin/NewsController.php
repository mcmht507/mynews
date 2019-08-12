<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\News;
use App\History;
use Carbon\Carbon;

class NewsController extends Controller
{
    public function add()
    {
        return view('admin.news.create');
    }

    public function create(Request $req)
    {
        $this->validate($req, News::$rules);

        $news = new News();
        $form = $req->all();

        if(isset($form['image'])){
            $path = $req->file('image')->store('public/image');
            $news->image_path = basename($path);
        }

        unset($form['_token']);
        unset($form['image']);

        $news->fill($form);
        $news->save();

        return redirect('admin/news/create');
    }

    public function index(Request $req)
    {
        $cond_title = $req->cond_title;
        if($cond_title != ''){
            $posts = News::where('title', $cond_title)->get();
        } else {
            $posts = News::all();
        }
        return view('admin.news.index', ['posts' => $posts, 'cond_title' => $cond_title]);
    }

    public function edit(Request $req)
    {
        $news = News::find($req->id);
        if(empty($news)){
            abort(404);
        }
        return view('admin.news.edit', ['news_form' => $news]);
    }

    public function update(Request $req)
    {
        $this->validate($req, News::$rules);
        $news = News::find($req->id);
        $news_form = $req->all();

        if(isset($news_form['image'])){
            $path = $req->file('image')->store('public/image');
            $news->image_path = basename($path);
            unset($news_form['image']);
        }elseif(0 == strcmp($req->remove,'true')){
            $news->image_path = null;
        }
        unset($news_form['_token']);
        unset($news_form['remove']);
        $news->fill($news_form)->save();
        // history
        $history = New History;
        $history->news_id = $news->id;
        $history->edited_at = Carbon::now();
        $history->save();

        return redirect('admin/news/');
    }

    public function delete(Request $req)
    {
        $news = News::find($req->id);
        $news->delete();
        return redirect('admin/news/');
    }
}
