<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\HTML;

use App\News;

class NewsController extends Controller
{
    public function index(Request $req)
    {
        $cond_title = $req->cond_title;

        if($cond_title != ''){
            $posts = News::where('title', $cond_title) . orderBy('updated_at', 'desc')->get();
        }else{
            $posts = News::all()->sortByDesc('updated_at');
        }

        if(count($posts)){
            $headline = $posts->shift();
        } else {
            $headline = null;
        }

        // news/index.blade.php ファイルを渡している
        // // また View テンプレートに headline、 posts、 cond_title という変数を渡している
        return view('news.index', ['headline' => $headline, 'posts' => $posts, 'cond_title' => $cond_title]);
    }
}
