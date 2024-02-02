class MailManager { 
    userEmail;
    userPseudo;
    constructor(userEmail, userPseudo) {
        this.userEmail = userEmail;
        this.userPseudo = userPseudo;
    }

    run = () => {
        document.getElementById('btn-view-events-day').addEventListener("click", (e) => {
            //console.log('click email du jour');
            this.handleClickBtnViewDay();
        });
    
        document.getElementById('btn-view-events-week').addEventListener("click", (e) => {
            //console.log('click email de la semaine');
            this.handleClickBtnViewWeek();
        });
    
        document.getElementById('btn-view-events-month').addEventListener("click", (e) => {
            //console.log('click email du mois');
            this.handleClickBtnViewMonth();
        }); 
    }




/*
document.addEventListener('DOMContentLoaded', () => {

//console.log('userEmail', userEmail, ' userPseudo', userPseudo);


});*/
/*
function run() {
    document.getElementById('btn-view-events-day').addEventListener("click", (e) => {
        //console.log('click email du jour');
        handleClickBtnViewDay();
    });

    document.getElementById('btn-view-events-week').addEventListener("click", (e) => {
        //console.log('click email de la semaine');
        handleClickBtnViewWeek();
    });

    document.getElementById('btn-view-events-month').addEventListener("click", (e) => {
        //console.log('click email du mois');
        handleClickBtnViewMonth();
    }); 
}
*/




handleClickBtnViewDay = async () => {

    const dateElement = document.getElementById('input-date-mail');

    if (dateElement.value !== "") {
        const day = dateElement.value;
        // appeler fonction pour faire requete pour récupérer les evenements du jour
        // get ds body : userEmail et day sur /mail/day/get
        const events = await RequestManager.getDayEventsFromDay(day);
        let html = '';
        if(events !== undefined) {
            events.sort((a, b) => new Date(a.start) - new Date(b.start));
            html = this.getDayHtmlFromDayEvents(events);
        } 
        else {
            html = `
                <h3>Pas d'événement</h3>
                `;
        }
        
        Swal.fire({
            title : `Evénement(s) du<br />${new Date(day).toLocaleDateString('fr-FR', {
                weekday: 'short',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
            })}`,

            html: html,
            icon: 'info',
            customClass: {
            container: 'custom-swal-container', // classe pour le conteneur principal de l'alerte
            popup: 'custom-swal-popup', // classe pour la fenêtre popup
            header: 'custom-swal-header', // classe pour l'en-tête
            title: 'custom-swal-title', // classe pour le titre
            closeButton: 'custom-swal-close-button', // classe pour le bouton de fermeture
            content: 'custom-swal-content', // classe pour le contenu
            confirmButton: 'custom-swal-confirm-button', // classe pour le bouton de confirmation
            },
            confirmButtonText: 'Retour'
        }); 
    }

}

handleClickBtnViewWeek = async () => {  
    
    const dateElement = document.getElementById('input-date-mail');

    if (dateElement.value !== "") {
        const day = dateElement.value;
        const events = await RequestManager.getWeekEventsFromDay(day);
        console.log('eventsWeek', events);

        let html = '';
        if(events !== undefined) {
            events.sort((a, b) => new Date(a.start) - new Date(b.start));
            html = this.getWeekHtmlFromDayEvents(events);
        } 
        else {
            html = `
                <h3>Pas d'événement</h3>
                `;
        }

        Swal.fire({
            title: `Evénements de la semaine`,
            html: html,
            icon: 'info',
            customClass: {
            container: 'custom-swal-container', // classe pour le conteneur principal de l'alerte
            popup: 'custom-swal-popup', // classe pour la fenêtre popup
            header: 'custom-swal-header', // classe pour l'en-tête
            title: 'custom-swal-title', // classe pour le titre
            closeButton: 'custom-swal-close-button', // classe pour le bouton de fermeture
            content: 'custom-swal-content', // classe pour le contenu
            confirmButton: 'custom-swal-confirm-button', // classe pour le bouton de confirmation
            },
            confirmButtonText: 'Retour'
        });
    }
    
    
}

handleClickBtnViewMonth = () => {

            
    Swal.fire({
        title: `Evénements du mois`,
        html: `
            <h3>html</h3>
            `,
        icon: 'info',
        customClass: {
        container: 'custom-swal-container', // classe pour le conteneur principal de l'alerte
        popup: 'custom-swal-popup', // classe pour la fenêtre popup
        header: 'custom-swal-header', // classe pour l'en-tête
        title: 'custom-swal-title', // classe pour le titre
        closeButton: 'custom-swal-close-button', // classe pour le bouton de fermeture
        content: 'custom-swal-content', // classe pour le contenu
        confirmButton: 'custom-swal-confirm-button', // classe pour le bouton de confirmation
        },
        confirmButtonText: 'Retour'
    });
}

/*
var getDayEventsForDay = async (day) => {
    //console.log('getEventsForDay', day);
    // faire requete pour récupérer les evenements du jour
    // get ds body : userEmail et day sur /mail/day/get
    events = [];
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

var getWeekEventsForDay = async (day) => {
    console.log('getWeekEventsForDay', day);
}
*/

getDayHtmlFromDayEvents = (events) => {
    return `
        <h3 class="text-orange alert-h3-mail">Nombre d'événement(s) : ${events.length}</h3>

        <table class="table">
            ${events.map((event, index) => `
                <tr>
                    <th colspan="2">
                    <span class="text-white"><strong>Evénement n°${index+1} R(${event.repeatNumber}) W(${event.weekNumber - 1})</strong></span>
                    </th>
                </tr>
                <tr>
                    <th><span class="text-orange"><strong>Nom</strong></span></th>
                    <td>${event.name}</td>
                </tr>
                <tr>
                    <th><span class="text-orange"><strong>Commentaire</strong></span></th>
                    <td>${event.comment}</td>
                </tr>
                <tr>
                    <th><span class="text-orange"><strong>Début</strong></span></th>
                    <td>${new Date(event.start).toLocaleDateString('fr-FR', {
                        weekday: 'short',
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                    })}</td>
                </tr>
                <tr>
                    <th><span class="text-orange"><strong>Fin</strong></span></th>
                    <td>${new Date(event.end).toLocaleDateString('fr-FR', {
                        weekday: 'short',
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                    })}</td>
                </tr>
                <tr>
                    <th><span class="text-orange"><strong>Catégorie</strong></span></th>
                    <td>${event.category}</td>
                </tr>                        
                <tr>
                    <th><span class="text-orange"><strong>Statut</strong></span></th>
                    <td>${event.status}</td>
                </tr>                          <tr>
                    <th><span class="text-orange"><strong>Priorité</strong></span></th>
                    <td>${event.priority}</td>
                </tr>
            `).join('')}
        </table>
    `;}

    getWeekHtmlFromDayEvents = (events) => {
        let oldEvent = null;
        let isNewDay = true;

        return `
            <h3 class="text-orange alert-h3-mail">Nombre d'événement(s) : ${events.length}</h3>
    
            <table class="table">
                ${events.map((event, index) => {
                    //console.log('event', event.start);
                    // event.start est une string de la forme 'yyyy-mm-dd mm:ss'
                    //let dateString = event.start.split(' ')[0];
                    // recuperer le jour depuis event.start

                    if (oldEvent === null || oldEvent.start.split(' ')[0] !== event.start.split(' ')[0]) {
                        isNewDay = true;
                    } else {
                        isNewDay = false;
                    }
                    oldEvent = event;
                    let dayTitleHtml = '';

                    if(isNewDay) {
                        dayTitleHtml = `
                        <tr>
                            <th colspan="2">
                                <span class="text-red">
                                    <strong>${new Date(event.start).toLocaleDateString('fr-FR', {
                                        weekday: 'long',
                                        year: 'numeric',
                                        month: 'long',
                                        day: '2-digit',
                                    })}</strong>
                                </span>
                            </th>
                        </tr>
                        `;
                    }
                    else {
                        dayTitleHtml = '';
                    }
    
                    return (dayTitleHtml + `
                    <tr>
                        <th colspan="2">
                        <span class="text-white"><strong>Evénement n°${index+1} R(${event.repeatNumber}) W(${event.weekNumber - 1})</strong></span>
                        </th>
                    </tr>
                    <tr>
                        <th><span class="text-orange"><strong>Nom</strong></span></th>
                        <td>${event.name}</td>
                    </tr>
                    <tr>
                        <th><span class="text-orange"><strong>Commentaire</strong></span></th>
                        <td>${event.comment}</td>
                    </tr>
                    <tr>
                        <th><span class="text-orange"><strong>Début</strong></span></th>
                        <td>${new Date(event.start).toLocaleDateString('fr-FR', {
                            weekday: 'short',
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                        })}</td>
                    </tr>
                    <tr>
                        <th><span class="text-orange"><strong>Fin</strong></span></th>
                        <td>${new Date(event.end).toLocaleDateString('fr-FR', {
                            weekday: 'short',
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                        })}</td>
                    </tr>
                    <tr>
                        <th><span class="text-orange"><strong>Catégorie</strong></span></th>
                        <td>${event.category}</td>
                    </tr>                        
                    <tr>
                        <th><span class="text-orange"><strong>Statut</strong></span></th>
                        <td>${event.status}</td>
                    </tr>                          <tr>
                        <th><span class="text-orange"><strong>Priorité</strong></span></th>
                        <td>${event.priority}</td>
                    </tr>
                `)
                }
                    ).join('')}
            </table>
        `;}

}