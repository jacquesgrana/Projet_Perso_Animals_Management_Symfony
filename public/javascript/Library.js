class Library {


    static getEmoticonFromAnimalCategory(category) {

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

    static getColorFromPriority(priority) {
        switch (priority) {
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