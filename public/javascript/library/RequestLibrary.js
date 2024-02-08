class RequestLibrary {

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


    // faire requete pour envoyer un mail
    // de type post sur /mail/day/send
    // avec le body : emailDest et day
    static sendDayMail(emailDest, day) {
        //console.log('sendMailDay', emailDest, day);
        const url = '/mail/day/send';
        const headers = new Headers({
            'Content-Type': 'application/json',
            'credentials': 'include'
        });
        const body = {
            'emailDest': emailDest,
            'day': day
        }
        console.log('body', body);
        return fetch(url, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(body)
        })
        // retourner le statut de la requete + texte de confirmation
        .then(response => {
            if (response.ok) {
                return response.text().then(text => {
                    return {
                        status: response.status,
                        confirmation: text
                    };
                });
            } else {
                throw new Error('Request failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            return {
                status: 500,
                confirmation: 'Request failed'
            };
        });
    }
    
}