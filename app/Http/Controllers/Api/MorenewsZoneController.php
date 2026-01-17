<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class MorenewsZoneController extends Controller
{
    protected const PER_PAGE = 1; // Constant for per page, easy to change
    protected const MAX_POSTS = 192; // Constant for max posts

    public function moreNewsZone($page, Request $request)
    {
        $perPage = self::PER_PAGE;
        $maxPosts = self::MAX_POSTS;
        $cacheKey = "morenews_zone_page_{$page}";
        $response = Cache::tags(['zone_posts'])->remember($cacheKey, now()->addMinutes(30), function () use ($perPage, $maxPosts, $page) {
            $posts = Post::where('status', 'publish')
                ->where('type', 'post')
                ->leftJoin('post_redirects', function ($join) {
                    $join->on('posts.reference_id', '=', 'post_redirects.id')
                        ->where('posts.item_type', '=', 'redirect');
                })
                ->with(['categories' => function ($query) {
                    $query->select('id', 'name', 'slug', 'parent_id'); // Tối ưu eager load
                }, 'author' => function ($query) {
                    $query->select('id', 'name'); // Tối ưu eager load
                }])
                ->orderBy('created_at', 'desc')
                ->select([
                    'posts.id', 'posts.title', 'posts.excerpt', 'posts.published_at', 'posts.updated_at',
                    'posts.image', 'posts.slug', 'posts.author_id', 'posts.created_at', 'posts.item_type',
                    'post_redirects.is_redirect', 'post_redirects.redirect_url',
                ])
                ->take($maxPosts)
                ->paginate($perPage, ['*'], 'page', $page);

            $contents = $posts->map(function ($post) {
                return $this->mapPostToContent($post);
            })->toArray();

            return [
                'data' => [
                    'contents' => $contents,
                    'load_more' => $posts->hasMorePages(),
                ],
                'error_code' => 0,
                'error_message' => 'Success',
                'server_time' => time(),
            ];
        });

        return response()->json($response);
    }

    public function moreNewsZoneCategory($categoryid, $page, Request $request)
    {
        $perPage = self::PER_PAGE;
        $maxPosts = self::MAX_POSTS;
        $cacheKey = "morenews_zone_category_{$categoryid}_page_{$page}";
        $response = Cache::tags(['category_posts', 'posts'])->remember($cacheKey, now()->addMinutes(30), function () use ($categoryid, $perPage, $maxPosts, $page) {
            $category = Cache::tags(['categories'])->remember("category_find_{$categoryid}", now()->addHours(6), function () use ($categoryid) {
                return Category::findOrFail($categoryid);
            });

            $categoryIds = Cache::tags(['category_data', 'categories'])->remember("category_ids_{$categoryid}", now()->addHours(6), function () use ($category) {
                return Category::where('left', '>=', $category->left)
                    ->where('right', '<=', $category->right)
                    ->pluck('id');
            });

            $posts = Post::join('category_post', 'posts.id', '=', 'category_post.post_id')
                ->whereIn('category_post.category_id', $categoryIds)
                ->where('posts.status', 'publish')
                ->where('posts.type', 'post')
                ->leftJoin('post_redirects', function ($join) {
                    $join->on('posts.reference_id', '=', 'post_redirects.id')
                        ->where('posts.item_type', '=', 'redirect');
                })
                ->with(['categories' => function ($query) {
                    $query->select('id', 'name', 'slug', 'parent_id'); // Tối ưu eager load
                }, 'author' => function ($query) {
                    $query->select('id', 'name'); // Tối ưu eager load
                }])
                ->orderBy('posts.created_at', 'desc')
                ->select([
                    'posts.id', 'posts.title', 'posts.excerpt', 'posts.published_at', 'posts.updated_at',
                    'posts.image', 'posts.slug', 'posts.author_id', 'posts.created_at', 'posts.item_type',
                    'post_redirects.is_redirect', 'post_redirects.redirect_url',
                ])
                ->distinct('posts.id')
                ->take($maxPosts)
                ->paginate($perPage, ['*'], 'page', $page);

            $contents = $posts->map(function ($post) {
                return $this->mapPostToContent($post);
            })->toArray();

            return [
                'data' => [
                    'contents' => $contents,
                    'load_more' => $posts->hasMorePages(),
                ],
                'error_code' => 0,
                'error_message' => 'Success',
                'server_time' => time(),
            ];
        });

        return response()->json($response);
    }

    // Giữ nguyên mapPostToContent() – nó xử lý slug/title tốt, sẽ rebuild sau flush
    protected function mapPostToContent($post)
    {
        $category = $post->categories->first();
        return [
            'content_id' => $post->id,
            'title' => $post->title,  // Sẽ cập nhật khi flush
            'sub_title' => '',
            'description' => $post->excerpt ?: Str::limit(strip_tags($post->excerpt ?? ''), 200),
            'date' => $post->published_at?->timestamp ?? time(),
            'update_time' => $post->updated_at?->timestamp ?? time(),
            'avatar_url' => $post->getThumbnailUrl('285x192'),
            'avatar_description' => '',
            'url' => $post->item_type === 'redirect' && $post->is_redirect ? $post->redirect_url : route('pages.show', ['slug' => $post->slug]),  // Slug sẽ mới sau rebuild
            'redirect_link' => $post->item_type === 'redirect' ? ($post->is_redirect ? $post->redirect_url : '') : '',
            'is_redirect' => $post->item_type === 'redirect' ? $post->is_redirect : false,
            'frame_link' => '',
            'display_type' => 0,
            'attributes' => 512,
            'content_type' => 'default',
            'content_icon' => '',
            'source' => 'SGGPO',
            'source_url' => '',
            'author' => $post->author?->name ?? 'Unknown',
            'show_title' => true,
            'show_sapo' => true,
            'show_avatar' => false,
            'show_comment' => true,
            'show_ads' => true,
            'show_audio' => true,
            'zone' => [
                'zone_id' => $category?->id ?? 24,
                'parent_id' => 0,
                'name' => $category?->name ?? 'Chính trị',
                'url' => $category?->getUrl() ?? '/chinhtri/',
            ],
            'song_type' => '',
            'song_author' => '',
        ];
    }
}