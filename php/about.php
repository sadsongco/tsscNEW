<?php

// include utility functions
include_once("includes/print_r2.php");

// initialise variables
$output = [];
$content = [];

    $content['title'] = "About";
    $content['content'] = "<!--{i::1::c}-->
    If you want to see a complete discography you can here of the records I've made you can <a id = \"linkTarget_p\" href = \"#discog\">here</a>, including selected sound clips.
    
    The Sad Song Co. is the project of Nigel Powell, previously writer / player / producer in Unbelievable Truth, also known for drumming Frank Turner And The Sleeping Souls and Dive Dive. The band also includes silent partner Jason Moulster, also previously of Unbelievable Truth, but he tends to keep himself to himself and stay in the background.
    
    Unbelievable Truth formed in 1996 and released their acclaimed debut album Almost Here in 1998 (#21 UK; two top 40 UK single hits; over 100,000 sales; worldwide global touring), and its follow up sorrythankyou in 2000. Andy Yorke left the band that year, so Nigel gathered the songs he had been writing under the umbrella of The Sad Song Co. name for his debut release miseryguts in 2003.
    
    Extensive hard work surrounding Dive Dive’s debut album Tilting At Windmills (top 20 UK indie chart album; 3 top 20 UK indie single chart hits) and producing the debut album The Bigger Picture for Belgian artist Milow (top 10 and certified gold in Belgium; top 20 single hit in Belgium, Holland, Germany and Switzerland) delayed the follow up Poignant Device until 2007, where it was launched during a special guest appearance at Marillion’s bi-annual convention in Port Zelande, Holland.
    
    During 2006 Dive Dive had become musical partners with Frank Turner, and his burgeoning popularity, combined with a relentless tour schedule (including headlining Wembley Arena and the O2 Arena in London; performing at the opening ceremony of the London 2012 Olympics; 6 full albums including 2 UK top 10 and a UK top 12), put The Sad Song Co. on ice for many years.
    
    In 2015 Nigel finally found time to return to his solo work and released the thematic album <i>in amber</i> in 2016 following a successful crowd funding campaign.
    
    Two years later saw the release of <i>Worth</i>, with support for its singles coming from BBC 6 Music and others.
    
    In 2020 he parted company with Frank Turner after 14 years and is concentrating on the release of his fifth album <i>Saudade</i> in early 2021, preceded by the singles <i>Feeding</i> and <i>My Saccharine</i>.
    
    <a href = \"mailto:info@thesadsongco.com\">Contact Me!</a>
    
    <hr />";

    $content['content'] .= "<h4 align = \"center\">What You Make Of It</h4>
    <p align = \"center\">Lyric Video</p>
    <!--{v::<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/VPZ_GFGv4ws?rel=0\" frameborder=\"0\" gesture=\"media\" allow=\"encrypted-media\" allowfullscreen></iframe>::c}-->

<hr />

    <h4 align = \"center\">What You Make Of It</h4>
    <p align = \"center\">Ben Morse interview - track by track</p>
    <!--{v::<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/UoPuci3fUAY?rel=0\" frameborder=\"0\" gesture=\"media\" allow=\"encrypted-media\" allowfullscreen></iframe>::c}-->

<hr />

    <h4 align = \"center\">Worth - the new album</h4>
    <p align = \"center\">Ben Morse interview pt 1</p>
    <!--{v::<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/OI4XDkJiXps?rel=0\" frameborder=\"0\" gesture=\"media\" allow=\"encrypted-media\" allowfullscreen></iframe>::c}-->

<hr />
    <h4 align = \"center\">Legacy Of Love</h4>
    <p align = \"center\">Lyric Video</p>
    <!--{v::<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/jAaqPDumM7s?rel=0\" frameborder=\"0\" allowfullscreen></iframe>::c}-->
<hr />
    <h4 align = \"center\">Meet You There</h4>
    <p align = \"center\">Lyric Video<br />
    <!--{v::<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/M_ErsuHw3to?rel=0\" frameborder=\"0\" allowfullscreen></iframe>::c}-->
    </p>
<hr />
    <h4 align = \"center\">Pledge Music campaign announcment video</h4>
    <!--{v::<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/B74i0eTCK5c?rel=0\" frameborder=\"0\" allowfullscreen></iframe>::c}-->";
    $content['images'][0]['url'] = "tssc4_s.jpeg";
    $content['images'][0]['title'] = "Nigel Powell - The Sad Song Co.";
    $content['images'][0]['credit'] = "photo by <a href = \"https://alyssanilsen.com/\" target = \"_blank\">Alyssa Nilsen</a>";

    $output[] = $content;
echo json_encode($output);

?>