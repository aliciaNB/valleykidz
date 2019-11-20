<?php

//Class that will build the table for printing
class BuildTable
{

    /**
     * Prints a default table of targets from current form. Adding id's to be targeted by a later ajax/json request.
     * @param $formId form id currently being viewed
     */
    public static function printEmptyTargets($formId)
    {
        $db = new database();
        $result=$db->getTargetsFromForm($formId);
        self::printTargetHeader();

        foreach ($result as $item)
        {
            $upper = ucwords($item['targetName']);
            echo"<tr><td rowspan='2'>";
            echo $upper;
            echo'<td>Urge</td>';
            $stripped = str_replace(' ', '', $item['targetName']);
            for($i=1; $i<8; $i++)
            {
                echo"<td class='empty text-center' id='"."tu". $stripped. ($i%7)."'>--</td>";
            }
            echo '</tr>';
            echo '<tr>';
            echo '<td>Action</td>';
            for($i=1; $i<8; $i++)
            {
                echo"<td class='empty text-center' id='"."ta". $stripped. ($i%7)."'>--</td>";
            }

        }
        //close table
        echo '</table></div>';//close table and div
    }


    /**
     * Prints a default table of emotions from current form. Adding id's to be targeted by a later ajax/json request.
     * @param $formId current form being worked with.
     */
    public static function printEmptyEmotions($formId)
    {

        $db = new database();
        $result=$db->getEmotionsFromForm($formId);
        self::printEmotionHeader();
        foreach ($result as $item) {
            $uppper = ucwords($item['emotionName']);
           echo '<tr><td>'.$uppper.'</td>';
            $stripped = str_replace(' ', '', $item['emotionName']);
           for($i=1; $i<8; $i++)
           {
               echo '<td class="empty text-center" id="'.'e'.$stripped.($i%7).'">--</td>';
           }
           echo '</tr>';
        }

        echo '</table></div>';//close table and div
    }

    /**
     * Prints a default table for all skills asssociated with current form. To be updated later by an ajax/json call.
     * Marking each td with an id to be used by the ajax request.
     * @param $formId current form number being viewed
     */
    public static function printEmptySkills($formId)
    {
        $db = new database();
        $result=$db->getSkillsFromForm($formId);
        self::printSkillHeader();

        foreach ($result as $core => $skills)
        {
            echo "<tr " . ($core === "Emotion Regulation" ? "class='paginateBefore'" : "") .">
                    <td rowspan=\"". sizeof($skills)*2 . "\">$core</td>";
            foreach ($skills as $key => $skill)
            {
                if ($key != 0)
                {
                    echo "<tr>";
                }

                echo "<td rowspan='2'>" . ucwords($skill['skillName']) . "</td>
                    <td>Degree</td>";

                $stripped = str_replace(' ', '', $skill['skillName']);
                for($i=1; $i<8; $i++)
                {
                    echo '<td class="empty text-center" id="'.'sd'.$stripped.($i%7).'">--</td>';
                }

                echo "</tr><tr><td>Used</td>";
                for($i=1; $i<8; $i++)
                {
                    echo '<td class="empty text-center" id="'.'su'.$stripped.($i%7).'">--</td>';
                }
                echo "</tr>";
            }
        }
        echo "</table>
            </div>";
    }

    /**
     * Prints header for emotions table
     */
    private static function printEmotionHeader()
    {
        //build table head
        echo'<h2 class="text-center mt-5 bgwhite">Emotions</h2>
        <div class="mt-5 table-responsive pagination">
            <table id="feelings" class="cell-border">
                <tr>
                    <th class="bglblue white">Emotions</th>';

        self::printDateRow();
    }

    /**
     * Prints header for targets table
     */
    private static function printTargetHeader()
    {
        echo "<h2 class=\"text-center mt-5\">Targets</h2>
                <div class=\" table-responsive pagination\">
                    <table id=\"targets\" class=\"cell-border\">
                        <tr>
                            <th class=\"bglblue white\" colspan=\"2\">Targets</th>";

        self::printDateRow();
    }

    /**
     * prints header for skllls table
     */
    private static function printSkillHeader()
    {
        echo "<h2 class=\"mt-5 text-center paginateBefore\">Skills</h2>
                <div class=\"mt-5 table-responsive\">
                    <table id=\"skill\" class=\"cell-border\">
                        <tr>
                            <th class=\"bglblue white\" colspan=\"3\">DBT Skills</th>";

        self::printDateRow();
    }

    /**
     * Prints th for date rows
     */
    private static function printDateRow()
    {
        $dates = array("Mon", "Tues", "Wed", "Thurs", "Fri", "Sat", "Sun");
        foreach ($dates as $date) {//dated in table head
            echo '<th class="bglblue white">'.$date.'</th>';
        }
        echo "</tr>";
    }
}