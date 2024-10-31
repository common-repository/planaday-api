<?php

class Planaday_date
{
    public static function current_date(): string {
        return date("Y-m-d");
    }


    public static function current_datetime(): string {
        return date("Y-m-d H:m:s", time());
    }


    public static function datetime_in_the_past($hours): string {
        $currentTime = time();
        $timeToSubtract = ($hours * 60 * 60);
        $timeInPast = $currentTime - $timeToSubtract;

        return date("Y-m-d H:m:s", $timeInPast);
    }


    public static function plus_months($amountOfMonths): string {
        return date("Y-m-d", strtotime( self::current_date() . " +" . $amountOfMonths . " months"));
    }


    public static function give_readable_date($date): string {
        $dayNumber = date("d", strtotime($date));
        $dayOfTheWeek = date("w", strtotime($date));
        $monthOfTheYear = date("n", strtotime($date)) - 1;
        $year = date("Y", strtotime($date));

        $days = ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'];
        $months = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
        $currentDay = $days[$dayOfTheWeek];
        $currentMonth = $months[$monthOfTheYear];

        return $currentDay . " " . $dayNumber . " " . $currentMonth . " " . $year;
    }


    public static function time_difference(?string $date1, ?string $date2): int {
		if ($date1 === null || $date2 === null) {
			return 0;
		}

        $dateDiff = (int)((strtotime($date1) - strtotime($date2)) / 60);
        return (int)($dateDiff / 60);
    }
}

