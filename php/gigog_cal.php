<?php

require_once("includes/std_g_includes.php");
include_once("includes/print_r2.php");

$query = "SELECT
        show_id,
        show_date,
        DATE_FORMAT(show_date, '%Y') AS show_year,
        DATE_FORMAT(show_date, '%m') AS show_month,
        DATE_FORMAT(show_date, '%d') AS show_day
    FROM shows ORDER BY show_date DESC;";

$stmt = $db->prepare($query);
$stmt->execute();
$shows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_show = array_pop($shows);

$cal_year = (int)$current_show['show_year'];
$cal_month = 1;
$date_track = new DateTime("$cal_year-01-01");
$interval = new DateInterval("P1D");
$today = new DateTime();
$this_year = (int)$today->format("Y");
$this_year = 2025;

$calendar_arr = [];
$curr_year_idx = 0;
$curr_mnth_idx = 0;
$curr_day_idx = 0;
array_push($calendar_arr ,["year"=>$cal_year, "months"=>[]]);
array_push($calendar_arr[$curr_year_idx]['months'], ["month"=>"January", "days"=>[]]);
$pos_y = 0;

while ($cal_year <= $this_year) {
    $cal_day = (int)$date_track->format("d");
    array_push($calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"], ["dayofweek"=>NULL, "date"=>"$cal_year-$cal_month-$cal_day", "shows"=>[]]);
    end($calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"]);
    $curr_day_idx = key($calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"]);
    $calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"][$curr_day_idx]['pos_x'] = (int)$date_track->format("w");
    $calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"][$curr_day_idx]['pos_y'] = $pos_y;
    if ((int)$date_track->format("w") === 6) $pos_y++;
    if (isset($current_show) && $current_show['show_date'] == $date_track->format("Y-m-d")) {
        array_push($calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"][$curr_day_idx]['shows'], [$current_show['show_id']]);
        if (!empty($shows)) $current_show = array_pop($shows);
        else unset($current_show);
        while(isset($current_show) && $current_show['show_date'] == $date_track->format("Y-m-d")) {
            array_push($calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"][$curr_day_idx]['shows'], [$current_show['show_id']]);
            if (!empty($shows)) $current_show = array_pop($shows);
        }
        $bg;
        switch (sizeof($calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"][$curr_day_idx]['shows'])) {
            case 1:
                $bg = "var(--is-show)";
                break;
            default:
                $bg = "var(--is-multi-show)"; 
                break;
        }
        $calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"][$curr_day_idx]['bg'] = $bg;
    } else {
        $calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"][$curr_day_idx]['shows'] = FALSE;
        $calendar_arr[$curr_year_idx]["months"][$curr_mnth_idx]["days"][$curr_day_idx]['bg'] = "var(--no-show)";
    }
    $date_track->add($interval);
    if ((int)$date_track->format("Y") == $this_year + 1) break;
    if ((int)$date_track->format("Y") > $cal_year) {
        $cal_year = (int)$date_track->format("Y");
        $calendar_arr[] = ["year"=>$cal_year, "months"=>[]];
        $curr_year_idx++;
        $cal_month = (int)$date_track->format("m");
        $cal_month_name = $date_track->format("F");
        array_push($calendar_arr[$curr_year_idx]["months"], ["month"=>$cal_month_name, "days"=>[]]);
        $curr_mnth_idx = 0;
        $pos_y = 0;
        continue;
    }
    if ((int)$date_track->format("m") != $cal_month) {
        $cal_month = (int)$date_track->format("m");
        $cal_month_name = $date_track->format("F");
        array_push($calendar_arr[$curr_year_idx]["months"], ["month"=>$cal_month_name, "days"=>[]]);
        $curr_mnth_idx++;
        $pos_y = 0;
    }
}

echo $m->render("gigog_calendar", ["calendar_arr"=>$calendar_arr]);
exit();

echo "<div class='calendar'>";
foreach ($calendar_arr AS $year=>$month) {
    echo "<h1>$year</h1>";
    echo "<div class='year'>";
    foreach ($month AS $month_num=>$days) {
        echo "<div class='month'>";
        $pos_y = 0;
        foreach ($days AS $date=>$day) {
            if (!$day['shows']) $bg = "var(--no-show)"; else $bg = "var(--is-show)";
            $pos_x = $day['dayofweek'];
            $style_str = "top: calc(var(--day-width) * $pos_y); left: calc(var(--day-width) * $pos_x); background-color: $bg;";
            echo "<div class='day' style='$style_str'></div>";
            if ($pos_x === 6) $pos_y++;
        }
        echo "</div>";
    }
    echo "</div>";
}
echo "</div>";
