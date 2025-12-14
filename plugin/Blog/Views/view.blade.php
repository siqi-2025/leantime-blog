@extends('layouts.app')

@section('content')
<div class="pageheader">
    <div class="pageicon"><i class="fa fa-file-text"></i></div>
    <div class="pagetitle">
        <h1>{{ $post->title }}</h1>
        <span class="subtitle">
            @if(isset($post->author_name))
                {{ __('作者') }}: {{ $post->author_name }}  &middot;
            @endif
            {{ __('发布于') }}: {{ date('Y-m-d H:i', strtotime($post->created_at)) }}
        </span>
    </div>
    <div class="pagetoolbar">
        <a href="{{ BASE_URL }}/blog/list" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> {{ __('返回列表') }}
        </a>
        <a href="{{ BASE_URL }}/blog/edit/{{ $post->id }}" class="btn btn-primary">
            <i class="fa fa-edit"></i> {{ __('编辑') }}
        </a>
    </div>
</div>

<div class="maincontent">
    <div class="maincontentinner">
        <div class="row">
            <div class="col-md-9">
                <!-- 文章元信息 -->
                <div class="post-meta" style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e5e5e5;">
                    @if(isset($post->category))
                    <span class="badge badge-info">
                        <i class="fa fa-folder"></i> {{ $post->category->name }}
                    </span>
                    @endif

                    @if($post->tags)
                    <span style="margin-left: 10px;">
                        <i class="fa fa-tags"></i>
                        @foreach(explode(',', $post->tags) as $tag)
                            <span class="label label-default">{{ trim($tag) }}</span>
                        @endforeach
                    </span>
                    @endif

                    <span style="float: right;">
                        <span class="label label-{{ $post->status === 'published' ? 'success' : 'warning' }}">
                            {{ $post->status === 'published' ? __('已发布') : __('草稿') }}
                        </span>
                    </span>
                </div>

                <!-- 文章内容 -->
                <div class="post-content markdown-body">
                    {!! $post->html_content !!}
                </div>

                <!-- 文章底部信息 -->
                <div class="post-footer" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e5e5;">
                    <p class="text-muted">
                        <i class="fa fa-clock-o"></i> {{ __('最后更新') }}: {{ date('Y-m-d H:i', strtotime($post->updated_at)) }}
                    </p>
                </div>
            </div>

            <!-- 侧边栏 -->
            <div class="col-md-3">
                <div class="widget">
                    <h4 class="widgettitle">{{ __('文章信息') }}</h4>
                    <ul class="list-unstyled">
                        <li><strong>ID:</strong> {{ $post->id }}</li>
                        <li><strong>{{ __('状态') }}:</strong>
                            <span class="label label-{{ $post->status === 'published' ? 'success' : 'warning' }}">
                                {{ $post->status === 'published' ? __('已发布') : __('草稿') }}
                            </span>
                        </li>
                        @if(isset($post->category))
                        <li><strong>{{ __('分类') }}:</strong> {{ $post->category->name }}</li>
                        @endif
                        <li><strong>{{ __('创建时间') }}:</strong><br>{{ $post->created_at }}</li>
                        <li><strong>{{ __('更新时间') }}:</strong><br>{{ $post->updated_at }}</li>
                    </ul>
                </div>

                <div class="widget">
                    <h4 class="widgettitle">{{ __('操作') }}</h4>
                    <ul class="list-unstyled">
                        <li>
                            <a href="{{ BASE_URL }}/blog/edit/{{ $post->id }}">
                                <i class="fa fa-edit"></i> {{ __('编辑文章') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ BASE_URL }}/blog/delete/{{ $post->id }}"
                               onclick="return confirm('确定要删除这篇文章吗？')">
                                <i class="fa fa-trash"></i> {{ __('删除文章') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ BASE_URL }}/blog/list">
                                <i class="fa fa-list"></i> {{ __('所有文章') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Markdown 样式 -->
<style>
.markdown-body {
    font-size: 16px;
    line-height: 1.6;
}
.markdown-body h1, .markdown-body h2, .markdown-body h3 {
    margin-top: 24px;
    margin-bottom: 16px;
    font-weight: 600;
    line-height: 1.25;
}
.markdown-body h1 {
    font-size: 2em;
    border-bottom: 1px solid #eaecef;
    padding-bottom: 0.3em;
}
.markdown-body h2 {
    font-size: 1.5em;
    border-bottom: 1px solid #eaecef;
    padding-bottom: 0.3em;
}
.markdown-body h3 {
    font-size: 1.25em;
}
.markdown-body code {
    padding: 0.2em 0.4em;
    margin: 0;
    font-size: 85%;
    background-color: rgba(27,31,35,0.05);
    border-radius: 3px;
}
.markdown-body pre {
    padding: 16px;
    overflow: auto;
    font-size: 85%;
    line-height: 1.45;
    background-color: #f6f8fa;
    border-radius: 3px;
}
.markdown-body blockquote {
    padding: 0 1em;
    color: #6a737d;
    border-left: 0.25em solid #dfe2e5;
}
.markdown-body img {
    max-width: 100%;
    height: auto;
}
.markdown-body table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 16px;
}
.markdown-body table th,
.markdown-body table td {
    padding: 6px 13px;
    border: 1px solid #dfe2e5;
}
.markdown-body table tr:nth-child(2n) {
    background-color: #f6f8fa;
}
</style>
@endsection
