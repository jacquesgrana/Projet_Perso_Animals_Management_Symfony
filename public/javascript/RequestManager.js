class RequestManager {

    static getDayEventsFromDay = async (day) => {
        //console.log('getEventsForDay', day);
        // faire requete pour récupérer les evenements du jour
        // get ds body : userEmail et day sur /mail/day/get
        const url = '/mail/day/get';
        const headers = new Headers({
            'Content-Type': 'application/json',
            'credentials': 'include'
        });
        
        return fetch(`${url}?day=${day}`, {
            method: 'GET',
            headers: headers,
        })
        .then(response => response.json())
        .then(datas => {
            //console.log('Success:', datas);
            return datas;
        })
        .catch((error) => {
            console.error('Error:', error);
            return [];
        });
    }
    
    static getWeekEventsFromDay = async (day) => {
        const url = '/mail/week/get';
        const headers = new Headers({
            'Content-Type': 'application/json',
            'credentials': 'include'
        });
        
        return fetch(`${url}?day=${day}`, {
            method: 'GET',
            headers: headers,
        })
        .then(response => response.json())
        .then(datas => {
            //console.log('Success:', datas);
            return datas;
        })
        .catch((error) => {
            console.error('Error:', error);
            return [];
        });
    }

    static getMonthEventsFromDay = async (day) => {
        const url = '/mail/month/get';
        const headers = new Headers({
            'Content-Type': 'application/json',
            'credentials': 'include'
        });
        
        return fetch(`${url}?day=${day}`, {
            method: 'GET',
            headers: headers,
        })
        .then(response => response.json())
        .then(datas => {
            //console.log('Success:', datas);
            return datas;
        })
        .catch((error) => {
            console.error('Error:', error);
            return [];
        });
    }
}