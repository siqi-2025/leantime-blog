<?php

namespace Leantime\Plugins\Blog;

use Leantime\Core\Events\EventDispatcher;
use Leantime\Domain\Plugins\Services\Registration;

class register
{
    public static function info(): array
    {
        return [
            'name' => 'Leantime Blog',
            'version' => '1.0.0',
            'description' => 'A comprehensive blog plugin with Markdown support, API integration, and external publishing',
            'author' => 'Claude Code',
            'homepage' => 'https://github.com/leantime/leantime-blog',
            'requires' => [
                'leantime' => '>=3.0.0',
                'php' => '>=8.1',
            ],
        ];
    }

    public static function boot(): void
    {
        // PSR-4 autoloader for plugin classes
        spl_autoload_register(function ($class) {
            $prefix = 'Leantime\\Plugins\\Blog\\';
            $base_dir = __DIR__ . '/';
            $len = strlen($prefix);

            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });

        // Use Leantime's official Registration service
        $registration = new Registration('Blog');

        // Add Blog menu to personal sidebar (left side menu)
        $registration->addMenuItem(
            [
                'title' => 'Blog',
                'icon' => 'fa fa-newspaper-o',
                'tooltip' => 'Manage blog posts',
                'href' => '/blog/list',
            ],
            'personal', // Personal menu section (left sidebar)
            [90] // Position after other items
        );

        // Also add to default project menu
        $registration->addMenuItem(
            [
                'title' => 'Blog',
                'icon' => 'fa fa-newspaper-o',
                'tooltip' => 'Blog posts',
                'href' => '/blog/list',
            ],
            'default', // Default project menu
            [45] // Position in menu
        );

        // Register API routes
        EventDispatcher::add_filter_listener(
            'leantime.core.api.jsonrpc',
            function ($methods) {
                return self::registerApiMethods($methods);
            }
        );
    }

    private static function registerApiMethods(array $methods): array
    {
        $methods['leantime.rpc.blog.listPosts'] = [
            'class' => '\\Leantime\\Plugins\\Blog\\Services\\BlogApiService',
            'method' => 'listPosts',
        ];

        $methods['leantime.rpc.blog.publishPost'] = [
            'class' => '\\Leantime\\Plugins\\Blog\\Services\\BlogApiService',
            'method' => 'publishPost',
        ];

        $methods['leantime.rpc.blog.uploadMarkdown'] = [
            'class' => '\\Leantime\\Plugins\\Blog\\Services\\BlogApiService',
            'method' => 'uploadMarkdown',
        ];

        $methods['leantime.rpc.blog.getPost'] = [
            'class' => '\\Leantime\\Plugins\\Blog\\Services\\BlogApiService',
            'method' => 'getPost',
        ];

        $methods['leantime.rpc.blog.updatePost'] = [
            'class' => '\\Leantime\\Plugins\\Blog\\Services\\BlogApiService',
            'method' => 'updatePost',
        ];

        $methods['leantime.rpc.blog.deletePost'] = [
            'class' => '\\Leantime\\Plugins\\Blog\\Services\\BlogApiService',
            'method' => 'deletePost',
        ];

        return $methods;
    }
}

// Call boot() when the file is included
register::boot();
