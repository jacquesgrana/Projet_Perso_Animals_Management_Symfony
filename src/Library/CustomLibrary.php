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
}
?>