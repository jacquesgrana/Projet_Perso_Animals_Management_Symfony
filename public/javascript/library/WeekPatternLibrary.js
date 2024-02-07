
    //traduire la derniere fonction en javascript
class WeekPatternLibrary {

    static weekNames = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
    static weekCodes = ["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"];
    static weekNamesEnglish = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

    static getDayNames(weekPattern){
        const weekPatternArray = weekPattern.split(';');
        let cpt = 0;
        const weekPatternNames = [];
        for (const day of weekPatternArray) {
            if (day === '1') {
                weekPatternNames.push(WeekPatternLibrary.weekNames[cpt]);
            }
            cpt++;
        }
        return weekPatternNames;
    }

    static getWeekPattern(weekPatternNames) {
        let weekPattern = '';
        for (let i = 0; i < 7; i++) {
            if (weekPatternNames.includes(WeekPatternLibrary.weekNames[i])) {
                weekPattern += '1;';
            } else {
                weekPattern += '0;';
            }
        }
        weekPattern = weekPattern.slice(0, -1);
        return weekPattern;
    }

    static getPatternAfterAddDay(weekPattern, dayToAdd) {
        const oldDays = WeekPatternLibrary.getDayNames(weekPattern);
        if (!oldDays.includes(dayToAdd)) {
            oldDays.push(dayToAdd);
        }
        return WeekPatternLibrary.getWeekPattern(oldDays);
    }


}


