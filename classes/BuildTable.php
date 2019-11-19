<?php

//Class that will build the table for printing
class BuildTable
{

    /**
     * Create an empty table of emotions for form
     * @param $startDate represents the start date of the form
     * @param $endDate representts
     * @param $formId
     * @throws Exception
     */
    public static function printEmptyEmotions($formId)
    {

        $db = new database();
        $result=$db->getEmotionsFromForm($formId);
        self::printEmotionHeader();
        foreach ($result as $item) {
           echo '<tr><td>'.$item['emotionName'].'</td>';
           for($i=1; $i<8; $i++)
           {
               echo '<td class="empty text-center" id="'.'t'.$item["emotionName"].($i%7).'">--</td>';
           }
           echo '</tr>';
        }

        echo '</table></div>';//close table and div
    }

    public static function printEmotions($startDate, $endDate, $formId)
    {
        $db = new database();
        $startDate = new DateTime($startDate);
        if($startDate->format("N")!==1)
        {
            $startDate= $startDate->modify('last monday');
            $startDate= $startDate->format("Y-m-d");
        }
        $endDate = new DateTime($endDate);
        $endDate = $endDate->format("Y-m-d");

        if(!$result){//print empty form as no results for emotions
            self::printEmptyEmotions($formId);

        } else{
            $dates = array("Mon", "Teus", "Wed", "Thurs", "Fri", "Sat", "Sun");
            $allEmotions = $db->getEmotionsFromForm($formId);
            $dateArray=[];
            self::printEmotionHeader();
            //start array for all entries




            foreach ($allEmotions as $allEmotion) {
                echo '<tr><td>'.$allEmotion['emotionName'].'</td>';
            }
        }
    }

    private static function printEmotionHeader()
    {
        $dates = array("Mon", "Teus", "Wed", "Thurs", "Fri", "Sat", "Sun"
        );
        //build table head
        echo'<h2 class="text-center mt-5 bgwhite">Emotions</h2>
        <div class="mt-5 table-responsive pagination">
            <table id="feelings" class="cell-border">
                <tr>
                    <th class="bglblue white">Emotions</th>';

        foreach ($dates as $date) {//dated in table head
            echo '<th class="bglblue white">'.$date.'</th>';
        }

        echo '</tr>';//close old tr and open new
    }
}