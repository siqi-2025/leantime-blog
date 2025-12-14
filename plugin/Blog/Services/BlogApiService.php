<?php

namespace Leantime\Plugins\Blog\Services;

use Illuminate\Support\Facades\DB;

/**
 * Blog API Service - Handles JSON-RPC API methods
 */
class BlogApiService
{
    /**
     * List blog posts
     *
     * @param array $params Parameters: status, limit, offset
     * @return array List of posts
     */
    public function listPosts(array $params = []): array
    {
        $status = $params['status'] ?? 'published';
        $limit = $params['limit'] ?? 10;
        $offset = $params['offset'] ?? 0;

        $query = DB::table('zp_blog_posts')
            ->whereNull('deleted_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $posts = $query->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return [
            'posts' => $posts,
            'total' => $query->count(),
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    /**
     * Get single post by ID
     *
     * @param array $params Parameters: id
     * @return array Post data
     */
    public function getPost(array $params): array
    {
        $postId = intval($params['id'] ?? 0);

        if ($postId <= 0) {
            throw new \Exception('Invalid post ID');
        }

        $post = DB::table('zp_blog_posts')
            ->where('id', $postId)
            ->whereNull('deleted_at')
            ->first();

        if (!$post) {
            throw new \Exception('Post not found');
        }

        // Get category if exists
        if ($post->category_id) {
            $category = DB::table('zp_blog_categories')
                ->where('id', $post->category_id)
                ->whereNull('deleted_at')
                ->first();
            $post->category = $category;
        }

        return ['post' => $post];
    }

    /**
     * Publish a new blog post
     *
     * @param array $params Parameters: title, content, category_id, tags, status
     * @return array Created post data
     */
    public function publishPost(array $params): array
    {
        $title = trim($params['title'] ?? '');
        $content = trim($params['content'] ?? '');
        $categoryId = intval($params['category_id'] ?? 0);
        $status = $params['status'] ?? 'published';
        $tags = trim($params['tags'] ?? '');

        // Validation
        if (empty($title)) {
            throw new \Exception('Title is required');
        }

        if (empty($content)) {
            throw new \Exception('Content is required');
        }

        if (!in_array($status, ['draft', 'published'])) {
            $status = 'draft';
        }

        // Insert post
        $postId = DB::table('zp_blog_posts')->insertGetId([
            'title' => $title,
            'content' => $content,
            'category_id' => $categoryId > 0 ? $categoryId : null,
            'tags' => $tags,
            'status' => $status,
            'author_id' => session("userdata.id") ?? 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $post = DB::table('zp_blog_posts')->where('id', $postId)->first();

        return [
            'success' => true,
            'post_id' => $postId,
            'post' => $post,
            'message' => $status === 'published' ? 'Post published successfully' : 'Post saved as draft',
        ];
    }

    /**
     * Update existing post
     *
     * @param array $params Parameters: id, title, content, category_id, tags, status
     * @return array Update result
     */
    public function updatePost(array $params): array
    {
        $postId = intval($params['id'] ?? 0);

        if ($postId <= 0) {
            throw new \Exception('Invalid post ID');
        }

        $post = DB::table('zp_blog_posts')
            ->where('id', $postId)
            ->whereNull('deleted_at')
            ->first();

        if (!$post) {
            throw new \Exception('Post not found');
        }

        $updateData = ['updated_at' => date('Y-m-d H:i:s')];

        if (isset($params['title'])) {
            $updateData['title'] = trim($params['title']);
        }

        if (isset($params['content'])) {
            $updateData['content'] = trim($params['content']);
        }

        if (isset($params['category_id'])) {
            $updateData['category_id'] = intval($params['category_id']) > 0 ? intval($params['category_id']) : null;
        }

        if (isset($params['tags'])) {
            $updateData['tags'] = trim($params['tags']);
        }

        if (isset($params['status']) && in_array($params['status'], ['draft', 'published'])) {
            $updateData['status'] = $params['status'];
        }

        DB::table('zp_blog_posts')
            ->where('id', $postId)
            ->update($updateData);

        $updatedPost = DB::table('zp_blog_posts')->where('id', $postId)->first();

        return [
            'success' => true,
            'post' => $updatedPost,
            'message' => 'Post updated successfully',
        ];
    }

    /**
     * Delete post (soft delete)
     *
     * @param array $params Parameters: id
     * @return array Delete result
     */
    public function deletePost(array $params): array
    {
        $postId = intval($params['id'] ?? 0);

        if ($postId <= 0) {
            throw new \Exception('Invalid post ID');
        }

        $post = DB::table('zp_blog_posts')
            ->where('id', $postId)
            ->whereNull('deleted_at')
            ->first();

        if (!$post) {
            throw new \Exception('Post not found');
        }

        DB::table('zp_blog_posts')
            ->where('id', $postId)
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);

        return [
            'success' => true,
            'message' => 'Post deleted successfully',
        ];
    }

    /**
     * Upload Markdown file and create post
     *
     * @param array $params Parameters: title, markdown_content, status
     * @return array Created post data
     */
    public function uploadMarkdown(array $params): array
    {
        $title = trim($params['title'] ?? '');
        $markdownContent = trim($params['markdown_content'] ?? '');
        $status = $params['status'] ?? 'draft';

        // Validation
        if (empty($title)) {
            throw new \Exception('Title is required');
        }

        if (empty($markdownContent)) {
            throw new \Exception('Markdown content is required');
        }

        // Use publishPost method
        return $this->publishPost([
            'title' => $title,
            'content' => $markdownContent,
            'status' => $status,
            'tags' => $params['tags'] ?? '',
            'category_id' => $params['category_id'] ?? 0,
        ]);
    }
}
