<?php

namespace Coyote\Http\Controllers\Microblog;

use Coyote\Http\Controllers\Controller;
use Coyote\Repositories\Eloquent\MicroblogRepository;
use Cache;

class HomeController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index(MicroblogRepository $repository)
    {
        $this->breadcrumb->push('Mikroblog', route('microblog.home'));

        $microblogs = $repository->paginate(25);

        // let's cache microblog tags. we don't need to run this query every time
        $tags = Cache::remember('microblogs-tags', 30, function () use ($repository) {
            return $repository->getTags();
        });

        // we MUST NOT cache popular entries because it may contains current user's data
        $popular = $repository->takePopular(5);

        $parser = app()->make('Parser\Microblog');

        foreach ($microblogs->items() as &$microblog) {
            $microblog->text = $parser->parse($microblog->text);
        }

        return parent::view('microblog.home', [
            'total'                     => $microblogs->total(),
            'pagination'                => $microblogs->render(),
            'microblogs'                => $microblogs->items()
        ])->with(compact('tags', 'popular'));
    }
}
