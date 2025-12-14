<?php

namespace Leantime\Plugins\Blog\Controllers;

use Illuminate\Support\Facades\DB;
use Leantime\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blog List Controller
 */
class List extends Controller
{
    /**
     * GET /blog/list
     */
    public function get(): Response
    {
        $posts = DB::table('zp_blog_posts')
            ->whereNull('deleted_at')
            ->where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->get();

        $this->tpl->assign('posts', $posts);

        return $this->tpl->display('blog.list');
    }
}
