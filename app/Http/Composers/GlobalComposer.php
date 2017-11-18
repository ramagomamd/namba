<?php

namespace App\Http\Composers;

use Illuminate\View\View;
use App\Repositories\Backend\Music\CacheRepository;

/**
 * Class GlobalComposer.
 */
class GlobalComposer
{
    protected $cache;

    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }
    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('logged_in_user', access()->user());
        if ($loading = $this->cache->findOrMake('settings', 'loading')) {
            $loading = $loading->getFirstMedia('image')->getFullUrl();
            $view->with('loading', $loading);
        }                                       
    }
}
