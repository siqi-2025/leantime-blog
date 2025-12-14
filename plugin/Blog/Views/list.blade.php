@extends('layouts.app')

@section('content')
<div class="pageheader">
    <div class="pageicon"><i class="fa fa-newspaper-o"></i></div>
    <div class="pagetitle">
        <h1>{{ __('博客文章') }}</h1>
        <span class="subtitle">{{ __('管理所有博客文章') }}</span>
    </div>
    <div class="pagetoolbar">
        <a href="{{ BASE_URL }}/blog/create" class="btn btn-primary">
            <i class="fa fa-plus"></i> {{ __('新建文章') }}
        </a>
    </div>
</div>

<div class="maincontent">
    <div class="maincontentinner">
        @if(count($posts) === 0)
            <div class="center padding-lg">
                <div style="padding: 60px 0;">
                    <i class="fa fa-file-text-o" style="font-size: 64px; color: #ccc;"></i>
                    <h3 style="margin-top: 20px; color: #999;">{{ __('暂无文章') }}</h3>
                    <p style="color: #999;">{{ __('点击上方"新建文章"按钮开始创建') }}</p>
                    <div style="margin-top: 30px;">
                        <a href="{{ BASE_URL }}/blog/create" class="btn btn-primary btn-lg">
                            <i class="fa fa-plus"></i> {{ __('创建第一篇文章') }}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover" id="allBlogPostsTable">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="40%">{{ __('标题') }}</th>
                            <th width="15%">{{ __('状态') }}</th>
                            <th width="15%">{{ __('作者') }}</th>
                            <th width="15%">{{ __('创建时间') }}</th>
                            <th width="10%">{{ __('操作') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posts as $post)
                        <tr class="post-item">
                            <td>{{ $post->id }}</td>
                            <td>
                                <a href="{{ BASE_URL }}/blog/view/{{ $post->id }}" style="font-weight: 500;">
                                    {{ $post->title }}
                                </a>
                                @if($post->tags)
                                    <br>
                                    <small class="text-muted">
                                        <i class="fa fa-tags"></i>
                                        @foreach(explode(',', $post->tags) as $tag)
                                            <span class="label label-default" style="margin-right: 5px;">{{ trim($tag) }}</span>
                                        @endforeach
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($post->status === 'published')
                                    <span class="label label-success">
                                        <i class="fa fa-check-circle"></i> {{ __('已发布') }}
                                    </span>
                                @else
                                    <span class="label label-warning">
                                        <i class="fa fa-clock-o"></i> {{ __('草稿') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <i class="fa fa-user"></i>
                                {{ $post->author_id ?? __('未知') }}
                            </td>
                            <td>
                                <i class="fa fa-calendar"></i>
                                {{ date('Y-m-d H:i', strtotime($post->created_at)) }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ BASE_URL }}/blog/view/{{ $post->id }}"
                                       class="btn btn-sm btn-default"
                                       title="{{ __('查看') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ BASE_URL }}/blog/edit/{{ $post->id }}"
                                       class="btn btn-sm btn-primary edit-button"
                                       title="{{ __('编辑') }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="{{ BASE_URL }}/blog/delete/{{ $post->id }}"
                                       class="btn btn-sm btn-danger delete-button"
                                       title="{{ __('删除') }}"
                                       onclick="return confirm('{{ __('确定要删除这篇文章吗？') }}')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 统计信息 -->
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12">
                    <p class="text-muted">
                        <i class="fa fa-info-circle"></i>
                        {{ __('共') }} <strong>{{ count($posts) }}</strong> {{ __('篇文章') }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.post-item:hover {
    background-color: #f5f5f5;
}
.btn-group .btn {
    margin-right: 2px;
}
</style>
@endsection
