<?php
namespace App\Library;
class CustomLibrary {
    public static function getEmoticonFromCategory($category)
    {
        switch ($category) {
            case "Chat":
                return "🐈";
            case "Chien":
                return "🐕";
            case "Poisson":
                return "🐟";
            case "Oiseau":
                return "🐦";
            case "Serpent":
                return "🐍";
            case "Insecte":
                return "🐞";
            case "Tortue":
                return "🐢";
            default:
                return "";
        }
    }

    public static function getColorFromPriority($priorityName): string {
        //dd($this->getPriority()->getName());
        switch ($priorityName) {
            case 'Non Urgente' :
                return 'green';
                break;
            case 'Normale' :
                return 'blue';
                break;
            case 'Urgente' :
                return 'orange';
                break;
            case 'Très Urgente' :
                return 'red';
                break;
            default :
                return 'white';
                break;
        }
    }
}
?>