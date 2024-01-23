//export class Library {


    function getEmoticonFromAnimalCategory(category) {

        switch (category) {
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
            default :
                return "";
        }
    }
//}