<?php

function zp_show_footer($is_usd = false)
{
    $curr = "USD";
    $curr = $is_usd ? "ZWL" : "USD";
    return "<h4>NB</strong></h4>
  <ul>
  <li>" . $curr . " equivalent prices are automatically updated by our system based on the prevailing market rates.</li>
 <li>This is done for informational purposes only.</li>
  <li>ZiG are automatically calculated by our systems based on current rates</li>

  </ul>";
}
function showCaption(string $req, array $captions)
{
    if (is_null($req)) {
        return "Price";
    }
    if (array_key_exists($req, $captions)) {
        return $captions[$req];
    }
    return ucwords($req);
}
