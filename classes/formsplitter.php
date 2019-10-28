<?php


class formsplitter
{
    static function printWeeks($startDate, $endDate, $formNum, $id)
    {
        $newDate = self::splitDates($startDate,$endDate);
        $divs ="";
        foreach ($newDate as $dates)
        {
            $futuredate = new DateTime($dates);
            $futuredate->modify('next saturday');
            $futuredate->format("Y-m-d");
            $divs.="<p><a href='viewform?form=".$formNum. "&weekStart". $dates."&weekEnd".$futuredate->format("Y-m-d")."&id".$id."'>". $dates.
                " - ". $futuredate->format("Y-m-d")."</a></p>";

        }
        return $divs;
    }

    private static function splitDates($startDate, $endDate)
    {
        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        $dates = [];

        if($startDate>$endDate)
        {
            return $dates;
        }
        if(7 !=$startDate->format('N'))
        {
            $startDate->modify('last monday');
        }
        while($startDate<=$endDate)
        {
            $dates[]=$startDate->format("Y-m-d");
            $startDate->modify("+1 week");
        }
        return $dates;
    }
}