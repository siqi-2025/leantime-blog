@extends('layouts.app')

@section('content')
<div class="pageheader">
    <div class="pageicon"><i class="fa fa-pencil"></i></div>
    <div class="pagetitle">
        <h1>{{ __('创建文章') }}</h1>
        <span class="subtitle">{{ __('发布新的博客文章') }}</span>
    </div>
</div>

<div class="maincontent">
    <div class="maincontentinner">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ BASE_URL }}/blog/create" method="post" id="createPostForm">
                    @csrf

                    <!-- 文章标题 -->
                    <div class="form-group">
                        <label for="title">{{ __('标题') }} <span class="required">*</span></label>
                        <input type="text"
                               name="title"
                               id="title"
                               class="form-control"
                               placeholder="请输入文章标题"
                               required
                               autofocus>
                    </div>

                    <!-- 分类选择 -->
                    @if(isset($categories) && count($categories) > 0)
                    <div class="form-group">
                        <label for="category_id">{{ __('分类') }}</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">-- 选择分类 --</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- 文章内容 -->
                    <div class="form-group">
                        <label for="content">{{ __('内容') }} <span class="required">*</span></label>
                        <textarea name="content"
                                  id="content"
                                  class="form-control"
                                  rows="15"
                                  placeholder="支持 Markdown 格式..."
                                  required></textarea>
                        <small class="form-text text-muted">
                            支持 Markdown 语法：**粗体**、*斜体*、[链接](url)、`代码`等
                        </small>
                    </div>

                    <!-- 标签 -->
                    <div class="form-group">
                        <label for="tags">{{ __('标签') }}</label>
                        <input type="text"
                               name="tags"
                               id="tags"
                               class="form-control"
                               placeholder="多个标签用逗号分隔，例如：技术,PHP,Leantime">
                    </div>

                    <!-- 状态和提交按钮 -->
                    <div class="form-group">
                        <button type="submit"
                                name="status"
                                value="published"
                                class="btn btn-primary">
                            <i class="fa fa-check"></i> {{ __('发布') }}
                        </button>
                        <button type="submit"
                                name="status"
                                value="draft"
                                class="btn btn-default">
                            <i class="fa fa-save"></i> {{ __('保存草稿') }}
                        </button>
                        <a href="{{ BASE_URL }}/blog/list" class="btn btn-default">
                            <i class="fa fa-times"></i> {{ __('取消') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SimpleMDE Markdown Editor (Optional) -->
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize SimpleMDE Markdown Editor
    var simplemde = new SimpleMDE({
        element: document.getElementById("content"),
        spellChecker: false,
        placeholder: "支持 Markdown 格式...\n\n# 标题\n## 副标题\n\n**粗体** *斜体*\n\n- 列表项 1\n- 列表项 2\n\n```php\n代码块\n```",
        toolbar: [
            "bold", "italic", "heading", "|",
            "quote", "unordered-list", "ordered-list", "|",
            "link", "image", "code", "table", "|",
            "preview", "side-by-side", "fullscreen", "|",
            "guide"
        ]
    });
});
</script>
@endsection
