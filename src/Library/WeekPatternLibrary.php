<?php
namespace App\Library;

class WeekPatternLibrary {

    public static $weekNames = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
    public static $weekCodes = ["MON", "TUE", "WEN", "THI", "FRI", "SAT", "SUN"];
    // un pattern est une string de la forme : '0;1;0;1;0;0;0'
    // avec 0 : pas d'event et 1 : event ce jour la
    // ici : mardi et jeudi : 0;1;0;1;0;0;0

    //fonction qui renvoi le nom des jours dans un tableau de string
    // en fonction du pattern
    public static function getDayNames($weekPattern): array{

        $weekPatternArray = explode(';', $weekPattern);
        $cpt = 0;
        $weekPatternName = [];
        foreach ($weekPatternArray as $day) {
            if($day == 1) {
                $weekPatternName[] = self::$weekNames[$cpt];
            }
            $cpt++;
        }
        return $weekPatternName;
    }

    // fonction inverse qui génère le pattern à partir d'un tableau de string ex ['lundi', 'mercredi', 'dimanche'] retourne '1;0;1;0;0;0;1'
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
}
?>