<div class="panel panel-default">
    <!-- Go to www.addthis.com/dashboard to customize your tools --> 
    <div style="display:inline-block; padding: 1em" class="addthis_inline_follow_toolbox"></div>
</div>
<div class="panel panel-info" align="center">
    <div style="display:inline-block; padding: 1em" class="fb-page" data-href="https://www.facebook.com/officialLulamusic/" 
        data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" 
        data-show-facepile="true">
        <blockquote cite="https://www.facebook.com/officialLulamusic/" class="fb-xfbml-parse-ignore">
            <a href="https://www.facebook.com/officialLulamusic/">LulaMusic Downloads</a>
        </blockquote>
    </div>
</div>
@include('frontend.music.trending')
<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-external-link"></i>&nbsp;
        <strong>Links</strong></h4>
    </div><!--panel-heading-->

    <ul class="list-group">
        <li class="list-group-item">
            <i class="fa fa-chevron-right"></i>&nbsp;
            <a href="{!! route('frontend.music.albums.index') !!}"
                style='{!! Active::checkUriPattern(["albums", "albums/*"]) ? "color: orange;" : ""!!}'>
                <strong>Albums</strong>
            </a>
        </li>
        <li class="list-group-item">
            <i class="fa fa-chevron-right"></i>&nbsp;
            <a href="{!! route('frontend.music.singles.index') !!}"
                style='{!! Active::checkUriPattern(["singles", "singles/*"]) ? "color: orange;" : ""!!}'>
                <strong>Singles</strong>
            </a>
        </li>
        <li class="list-group-item">
            <i class="fa fa-chevron-right"></i>&nbsp;
            <a href="{!! route('frontend.music.tracks.index') !!}"
                style='{!! Active::checkUriPattern(["tracks", "tracks/*"]) ? "color: orange;" : ""!!}'>
                <strong>Tracks</strong>
            </a>
        </li>
        <li class="list-group-item">
            <i class="fa fa-chevron-right"></i>&nbsp;
            <a href="{!! route('frontend.music.artists.index') !!}"
                style='{!! Active::checkUriPattern(["artists", "artists/*"]) ? "color: orange;" : ""!!}'>
                <strong>Artists</strong>
            </a>
        </li>
        <li class="list-group-item">
            <i class="fa fa-chevron-right"></i>&nbsp;
            <a href="{!! route('frontend.music.genres.index') !!}"
                style='{!! Active::checkUriPattern(["genres", "genres/*"]) ? "color: orange;" : ""!!}'>
                <strong>Genres</strong>
            </a>
        </li>
        <li class="list-group-item">
            <i class="fa fa-chevron-right"></i>&nbsp;
            <a href="{!! route('frontend.contact') !!}"
                style='{!! Active::checkUriPattern("contact") ? "color: orange;" : ""!!}'>
                <strong>DMCA</strong>
            </a>
        </li>
    </ul><!--panel-body-->
</div><!--panel-->