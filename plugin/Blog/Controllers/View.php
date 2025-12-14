<?php

namespace Leantime\Plugins\Blog\Controllers;

use Illuminate\Support\Facades\DB;
use Leantime\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blog View Controller - Display single post
 */
class View extends Controller
{
    /**
     * GET /blog/view/{id} - Display single post
     */
    public function get($params): Response
    {
        $postId = intval($params['id'] ?? 0);

        if ($postId <= 0) {
            $this->tpl->setNotification('文章ID无效', 'error');
            return redirect('/blog/list');
        }

        // Get post details
        $post = DB::table('zp_blog_posts')
            ->where('id', $postId)
            ->whereNull('deleted_at')
            ->first();

        if (!$post) {
            $this->tpl->setNotification('文章不存在', 'error');
            return redirect('/blog/list');
        }

        // Get category if exists
        if ($post->category_id) {
            $category = DB::table('zp_blog_categories')
                ->where('id', $post->category_id)
                ->whereNull('deleted_at')
                ->first();
            $post->category = $category;
        }

        // Get author info (if available)
        if ($post->author_id) {
            $author = DB::table('zp_user')
                ->where('id', $post->author_id)
                ->first();
            if ($author) {
                $post->author_name = ($author->firstname ?? '') . ' ' . ($author->lastname ?? '');
                $post->author_email = $author->username ?? '';
            }
        }

        // Render Markdown content (if needed)
        if (class_exists('\Parsedown')) {
            $parsedown = new \Parsedown();
            $post->html_content = $parsedown->text($post->content);
        } else {
            $post->html_content = nl2br(htmlspecialchars($post->content));
        }

        $this->tpl->assign('post', $post);
        $this->tpl->assign('pageTitle', $post->title);

        return $this->tpl->display('blog.view');
    }
}
