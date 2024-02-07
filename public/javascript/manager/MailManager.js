class MailManager { 
    userEmail;
    userPseudo;
    constructor(userEmail, userPseudo) {
        this.userEmail = userEmail;
        this.userPseudo = userPseudo;
    }

    run = () => {
        document.getElementById('btn-view-events-day').addEventListener("click", (e) => {
            e.preventDefault();
            this.handleClickBtnViewDay();
        });
    
        document.getElementById('btn-view-events-week').addEventListener("click", (e) => {
            e.preventDefault();
            this.handleClickBtnViewWeek();
        });
    
        document.getElementById('btn-view-events-month').addEventListener("click", (e) => {
            e.preventDefault();
            this.handleClickBtnViewMonth();
        });
        
        document.getElementById('btn-send-day-mail').addEventListener("click", (e) => {
            e.preventDefault();
            this.handleClickBtnSendMailDay();
        });

        document.getElementById('btn-send-week-mail').addEventListener("click", (e) => {
            e.preventDefault();
            this.handleClickBtnSendMailWeek();
        });

        document.getElementById('btn-send-month-mail').addEventListener("click", (e) => {
            e.preventDefault();
            this.handleClickBtnSendMailMonth();
        });
    }




/*
document.addEventListener('DOMContentLoaded', () => {

//console.log('userEmail', userEmail, ' userPseudo', userPseudo);


});*/

handleClickBtnSendMailDay = async () => {
    console.log('click send mail day');
    // afficher fenetre swal de confirmation
    const result = await Swal.fire({
        title: 'Confirmation',
        text: "Veuillez confirmer votre choix",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: 'Annuler',
        confirmButtonText: 'Envoyer email',
        //confirmButtonColor: '#3085d6',
        //cancelButtonColor: 'orange',
        
        customClass: {
            icon: 'custom-swal-icon',
            container: 'custom-swal-container', // classe pour le conteneur principal de l'alerte
            popup: 'custom-swal-popup', // classe pour la fenêtre popup
            header: 'custom-swal-header', // classe pour l'en-tête
            title: 'custom-swal-title', // classe pour le titre
            content: 'custom-swal-content', // classe pour le contenu
            cancelButton: 'custom-swal-cancel-button', // classe pour le bouton d'annulation
            confirmButton: 'custom-swal-confirm-button', // classe pour le bouton de confirmation
            
            }
    });

    if (result.isConfirmed) {
        console.log('click confirm send mail day');

        // recupérer l'email de destination et la date du jour
        const emailDest = document.getElementById('input-email-mail').value;

        const day = document.getElementById('input-date-mail').value;

        console.log('emailDest', emailDest, 'day', day);
        // appeler fonction pour faire requete pour envoyer le mail sur la route /mail/day/send
        const response = await RequestLibrary.sendMailDay(emailDest, day);
        console.log('response', response);
    }
    

}

handleClickBtnSendMailWeek = async () => {
    console.log('click send mail week');
}

handleClickBtnSendMailMonth = async () => {
    console.log('click send mail month');
}

handleClickBtnViewDay = async () => {

    const dateElement = document.getElementById('input-date-mail');

    if (dateElement.value !== "") {
        const day = dateElement.value;
        // appeler fonction pour faire requete pour récupérer les evenements du jour
        // get ds body : userEmail et day sur /mail/day/get
        const events = await RequestLibrary.getDayEventsFromDay(day);
        let html = '';
        if(events !== undefined && events.length > 0) {
            events.sort((a, b) => new Date(a.start) - new Date(b.start));
            html = this.getDayHtmlFromEvents(events);
        } 
        else {
            html = `
                <h3><span class="text-orange">Pas d'événement(s)</span></h3>
                `;
        }
        
        Swal.fire({
            title : `Evénement(s) du<br />${new Date(day).toLocaleDateString('fr-FR', {
                weekday: 'long',
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

    // calculer la date du debut et de la fin de la semaine
    const weekStart = this.getCurrentWeekMonday(new Date(dateElement.value));
    // calculer le lundi de la semaine correspondante
    //console.log('weekStart', weekStart);

    const nextSunday = new Date(weekStart);
    nextSunday.setDate(nextSunday.getDate() + 6);


    if (dateElement.value !== "") {
        const day = dateElement.value;
        const events = await RequestLibrary.getWeekEventsFromDay(day);
        //console.log('eventsWeek', events);

        let html = '';
        if(events !== undefined && events.length > 0) {
            events.sort((a, b) => new Date(a.start) - new Date(b.start));
            html = this.getWeekHtmlFromEvents(events);
        } 
        else {
            html = `
                <h3><span class="text-orange">Pas d'événement(s)</span></h3>
                `;
        }

        Swal.fire({
            title: `Evénements de la semaine <br />du ${weekStart.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: '2-digit',
            })} <br />au ${nextSunday.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
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

handleClickBtnViewMonth = async () => {
    const dateElement = document.getElementById('input-date-mail');
    
    if (dateElement.value !== "") {
        const day = dateElement.value;
        const events = await RequestLibrary.getMonthEventsFromDay(day);
        //console.log('eventsMonth', events);
        // récupérer le nom du mois à partir de day ('2023-01-01')
        const monthName = new Date(day).toLocaleDateString('fr-FR', {
            month: 'long',
            year: 'numeric'
        })
        let html = '';
        if(events !== undefined && events.length > 0) {
            events.sort((a, b) => new Date(a.start) - new Date(b.start));
            html = this.getMonthHtmlFromEvents(events);
        } 
        else {
            html = `
                <h3><span class="text-orange">Pas d'événement(s)</span></h3>
                `;
        }
        Swal.fire({
            title: `Evénements du mois <br />de ${monthName}`,
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

getDayHtmlFromEvents = (events) => {
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

getWeekHtmlFromEvents = (events) => {
    let oldEvent = null;
    let isNewDay = true;

    return `
        <h3 class="text-orange alert-h3-mail">Nombre d'événement(s) : ${events.length}</h3>

        <table class="table">
            ${events.map((event, index) => {
                isNewDay = (oldEvent === null || oldEvent.start.split(' ')[0] !== event.start.split(' ')[0]) ? true : false;
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
    `;
}

getMonthHtmlFromEvents = (events) => {
    let oldEvent = null;
    let isNewDay = true;

    return `
        <h3 class="text-orange alert-h3-mail">Nombre d'événement(s) : ${events.length}</h3>

        <table class="table">
            ${events.map((event, index) => {
                isNewDay = (oldEvent === null || oldEvent.start.split(' ')[0] !== event.start.split(' ')[0]) ? true : false;
                oldEvent = event;
                let dayTitleHtml = '';
                let weekTitleHtml = '';
                // si event.start est un lundi -> on ajoute un <tr> pour le titre de la semaine au premier evenement du jour
                if(new Date(event.start).getDay() === 1) {
                    weekTitleHtml = `
                    <tr>
                        <th colspan="2">
                            <span class="text-green">
                                <strong>Semaine du ${new Date(event.start).toLocaleDateString('fr-FR', {
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

                weekTitleHtml = (isNewDay) ? weekTitleHtml : '';

                return (weekTitleHtml + dayTitleHtml + `
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
    `;
}


getCurrentWeekMonday = (date) => {
    const dayOfWeek = date.getDay();
    const difference = date.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
    date.setDate(difference);
    return date;
}

}