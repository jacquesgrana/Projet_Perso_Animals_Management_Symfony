<?php
namespace App\Library;

class WeekPatternLibrary {

    public static $weekNames = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
    public static $weekCodes = ["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"];
    public static $weekNamesEnglish = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

    // un pattern est une string de la forme : '0;1;0;1;0;0;0'
    // avec 0 : pas d'event et 1 : event ce jour la
    // ici : Mardi et Jeudi : 0;1;0;1;0;0;0

    //fonction qui renvoi le nom des jours dans un tableau de string
    // en fonction du pattern
    public static function getDayNames($weekPattern): array{
        $weekPatternArray = explode(';', $weekPattern);
        $cpt = 0;
        $weekPatternNames = [];
        foreach ($weekPatternArray as $day) {
            if($day == 1) {
                $weekPatternNames[] = self::$weekNames[$cpt];
            }
            $cpt++;
        }
        return $weekPatternNames;
    }

    // fonction inverse qui génère le pattern à partir d'un tableau de string ex ['Lundi', 'Mercredi', 'Dimanche'] retourne '1;0;1;0;0;0;1'
    public static function getWeekPattern($weekPatternNames): string{
        //dd($weekPatternNames);
        // si $weekPatternNames n'est pas un tableau mais une string, on le transforme en tableau contenant un element qui est la string
        if(!is_array($weekPatternNames)) {
            $weekPatternNames = [$weekPatternNames];
        }
        $weekPattern = '';
        for ($i = 0; $i < 7; $i++) {
            if(in_array(self::$weekNames[$i], $weekPatternNames)) {
                $weekPattern .= '1;';
            } else {
                $weekPattern .= '0;';
            }
            //
        }
        $weekPattern = substr($weekPattern, 0, -1);
        //dd($weekPattern);
        return $weekPattern;
    }

        // fonction qui ajoute à un pattern un jour de la semaine
        public static function getPatternAfterAddDay($weekPattern, $dayToAdd): string{
            $oldDays = Static::getDayNames($weekPattern);
            if (!in_array($dayToAdd, $oldDays)) {
                $oldDays[] = $dayToAdd;
            }
            
            return Static::getWeekPattern($oldDays);
        }

    // fonction avec un switch/case qui transforme un nom de jour en anglais en nom de jour français    
    public static function getFrenchDayName($dayName): string{
        switch ($dayName) {
            case 'Monday':
                return 'Lundi';
            case 'Tuesday':
                return 'Mardi';
            case 'Wednesday':
                return 'Mercredi';
            case 'Thursday':
                return 'Jeudi';
            case 'Friday':
                return 'Vendredi';
            case 'Saturday':
                return 'Samedi';
            case 'Sunday':
                return 'Dimanche';
            default:
                return $dayName;
        }
    }

    //fonction inverse de getFrenchDayName
    public static function getEnglishDayName($dayName): string{
        switch ($dayName) {
            case 'Lundi':
                return 'Monday';
            case 'Mardi':
                return 'Tuesday';
            case 'Mercredi':
                return 'Wednesday';
            case 'Jeudi':
                return 'Thursday';
            case 'Vendredi':
                return 'Friday';
            case 'Samedi':
                return 'Saturday';
            case 'Dimanche':
                return 'Sunday';
            default:
                return $dayName;
        }
    }

}
?>