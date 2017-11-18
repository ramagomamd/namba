<?php

namespace App\Http\Controllers\Frontend\Music;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Music\SingleRepository;
use App\Models\Music\Single\Single;
use SEOMeta;
use OpenGraph;
use Twitter;

class SinglesController extends Controller
{
    protected $singles;

    public function __construct(SingleRepository $singles)
    {
        $this->singles = $singles;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "All Singles";

        $singles = $this->singles->query()
                ->has('track')
                ->with('track')
                ->latest()
                ->paginate(10);

        $description = 'Stream and Download Full South African and International MP3 Music Singles. Download Single Songs Individually or Download a Full Zipped Single Free at NambaNamba.COM';
        $url = route('frontend.music.singles.index');

        // SEO Tags
        SEOMeta::setTitle($title)
                ->setDescription($description)
                ->setCanonical($url)
                ->addKeyword([
                            'south african hip hop mp3 singles downloads', 
                            'mzansi hip hop zip singles download', 
                            'south african house music downloads', 
                            'international hip hop mp3 singles downloads'
                ])
                ->addMeta('robots', 'noindex,follow');

        OpenGraph::setDescription($description)
                    ->setTitle($title)
                    ->setUrl($url)
                    ->addProperty('type', 'music.singles');

        Twitter::setTitle($title)
                ->setSite('@NambaNamba_Downloads');

        return view('frontend.music.singles.index', compact('title', 'singles', 'description'));
    }

    public function create()
    {
        $description = "create single";
        return view('frontend.music.singles.create', compact('description'));
    }

    public function store()
    {
        $results = $this->singles->create(request(['file', 'category', 'genre', 'description']));

        return response($results['message'], $results['code']);
    }
}
