<?php

namespace Leantime\Plugins\Blog\Controllers;

use Illuminate\Support\Facades\DB;
use Leantime\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blog Create Controller
 */
class Create extends Controller
{
    /**
     * GET /blog/create - Display create form
     */
    public function get(): Response
    {
        // Get categories for dropdown (if needed)
        $categories = DB::table('zp_blog_categories')
            ->whereNull('deleted_at')
            ->orderBy('name', 'ASC')
            ->get();

        $this->tpl->assign('categories', $categories);
        $this->tpl->assign('pageTitle', '创建文章');

        return $this->tpl->display('blog.create');
    }

    /**
     * POST /blog/create - Handle form submission
     */
    public function post(): Response
    {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $status = $_POST['status'] ?? 'draft';
        $tags = trim($_POST['tags'] ?? '');

        // Validation
        if (empty($title)) {
            $this->tpl->setNotification('标题不能为空', 'error');
            return $this->get();
        }

        if (empty($content)) {
            $this->tpl->setNotification('内容不能为空', 'error');
            return $this->get();
        }

        // Validate status
        if (!in_array($status, ['draft', 'published'])) {
            $status = 'draft';
        }

        try {
            // Insert post
            $postId = DB::table('zp_blog_posts')->insertGetId([
                'title' => $title,
                'content' => $content,
                'category_id' => $category_id > 0 ? $category_id : null,
                'tags' => $tags,
                'status' => $status,
                'author_id' => session("userdata.id") ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $message = $status === 'published' ? '文章发布成功' : '文章保存为草稿';
            $this->tpl->setNotification($message, 'success');

            // Redirect to view or list
            return redirect('/blog/view/' . $postId);

        } catch (\Exception $e) {
            error_log('Blog create error: ' . $e->getMessage());
            $this->tpl->setNotification('创建文章失败：' . $e->getMessage(), 'error');
            return $this->get();
        }
    }
}
