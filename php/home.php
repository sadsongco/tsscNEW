<?php

// include utility functions
include_once("includes/print_r2.php");

// initialise variables
$output = [];
$content = [];

    $content['title'] = "Homepage";
    $content['content'] = "<!--{i::1::c}-->
    <h3>Saudade - the new album - out now</h3>

    My fifth album Saudade is out now. It's an emotional and personal work, and I'm very proud of it, and excited to get it out into the world. You can get it wherever you consume your digital music. If you want a limited edition CD or t-shirt (both come with a download of the album) you can get it at Bandcamp - links below.

    <a href = \"https://thesadsongco.bandcamp.com/album/saudade\" target = \"_blank\">Bandcamp</a>
    <a href = \"https://apple.co/2Mf6Cw8\" target = \"_blank\">iTunes / Apple Music</a>
    <a href = \"https://open.spotify.com/album/1ziDvxukozqw97XI6C6Hm0?si=XPJ6IkmMS8CkM0hrqlfp9g\" target = \"_blank\">Spotify</a>

    Catch up with me on  <a href = \"https://www.instagram.com/sadsongco/\" target = \"_blank\">Instagram Live</a> Wednesdays at 7pm UK time, or catch it on a replay. Chatting to music friends about all sorts, or playing some songs.

    Stay in touch by signing up for the <div id = \"mc_show_2\">mailing list</div>, or following on your choice of social medias (links at bottom of page).";

    $content['images'][0]['url'] = "TSSCSaudadeCoverCarousel.jpg";
    $content['images'][0]['title'] = "Saudade - the new album - out now";
    $content['images'][0]['credit'] = "Artwork by Alice Ibsen";

    $output[] = $content;
echo json_encode($output);

?>