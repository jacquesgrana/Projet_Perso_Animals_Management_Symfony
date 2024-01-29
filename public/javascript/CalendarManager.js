class CalendarManager {

    eventsToShow = [];
    constructor(eventsToShow) {
        this.eventsToShow = eventsToShow;
    }

    run() {
        document.addEventListener('DOMContentLoaded', () => {
            //console.log('DOM Content Loaded.');
            const calendarEl = document.getElementById('calendar-holder');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                editable: true,
                eventClassNames: ['calendar-class'],
        
                // Your FullCalendar options go here
                // ...
                buttonText: {
                    prev: 'Précédent',
                    next: 'Suivant',
                    today: 'Aujourd\'hui',
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste',
                },
                locale: 'fr',
                headerToolbar: {
                    start: 'prev,next today',
                    center: 'title',
                    end: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                views: {
                    dayGridMonth: { // name of view
                    titleFormat: { year: 'numeric', month: 'long' }
                    // other view-specific options here
                    }
                    
                }
            });
            /*
            //console.log('eventsToShow :', this.eventsToShow);
            this.eventsToShow.forEach((event) => {
                // add event.duration to event.start to calculate end
                const endDate = new Date(event.start);
                const durationInMinutes = event.duration;
                const hours = Math.floor(durationInMinutes / 60);
                const minutes = durationInMinutes % 60;
                endDate.setHours(endDate.getHours() + hours);
                endDate.setMinutes(endDate.getMinutes() + minutes);
                endDate.setDate(endDate.getDate());
                calendar.addEvent({
                    title: `${event.name}`,
                    start: event.start,
                    end: endDate,
                    id: event.id,
                    comment: event.comment,
                    priority: event.priority,
                    status: event.status,
                    category: event.category,
                    animals: event.animals,
                    patternNumber: event.patternNumber,
                    weekPattern: event.weekPattern,
                    backgroundColor: Library.getColorFromPriority(event.priority),
                    
                });
            });
*/

            /*
            // boucle sur eventsToShow avec event
                // si event.patternsNumber > 0
                    
                    // boucle sur patternsNumber j de 1 à event.patternNumber
                        
                        // determiner le 'Lundi' de la semaine de l'event
                        // boucle de 0 a 7 sur i
                            // si tableau du event.weekPatter[i] === '1'
                            
                                // calcul du décalage : i + (j-1)*7
                                // calcul du jour : lundi + decalage
                                // si jour ==  jour du event.start 
                                    // ajout de l'event de ce jour la avec mention que c'est l'event racine
                                // si jour >= jour du event.start
                                    // ajout de l'event de ce jour la avec mention que ce n'est pas l'event racine
                        

            */

                                    
           // traduction en js du code ci-dessus
           this.eventsToShow.forEach((event) => {
                //console.log('event :', event);
               if (event.patternsNumber > 0) {
                    for (let j = 1; j <= event.patternsNumber; j++) {
                        // determiner le 'Lundi' de la semaine de l'event
                        const eventDate = event.start;
                        // Convertissez la chaîne en objet moment
                        const eventStart = moment(eventDate, 'YYYY-MM-DD HH:mm:ss');
                        const hours = eventStart.hours();
                        const minutes = eventStart.minutes();
                        const seconds = eventStart.seconds();
                        const minutDuration = event.duration;
                        // Obtenez le lundi de la semaine de la date
                        const mondayOfTheWeek = eventStart.clone().startOf('isoWeek');

                        // Affichez la date du lundi
                        //console.log('lundi de la semaine :', mondayOfTheWeek.format('YYYY-MM-DD HH:mm:ss'));

                        //console.log('eventDate :', eventDate);
                        const weekPattern = event.weekPattern.split(';');
        
                        for (let i = 0; i < 7; i++) {
                            if (weekPattern[i] === '1') {
                                const deltaDay = i + (j - 1) * 7;
                                const dayObject = mondayOfTheWeek.clone().add(deltaDay, 'days');
                                dayObject.hours(hours).minutes(minutes).seconds(seconds);
                                const eventEnd = dayObject.clone().add(minutDuration, 'minutes');
                                
                                if (eventDate === dayObject.format('YYYY-MM-DD HH:mm:ss')) {
                                    calendar.addEvent({
                                        title: `R: ${event.name}`,
                                        start: dayObject.format('YYYY-MM-DD HH:mm:ss'),
                                        end: eventEnd.format('YYYY-MM-DD HH:mm:ss'),
                                        id: event.id,
                                        comment: event.comment,
                                        priority: event.priority,
                                        status: event.status,
                                        category: event.category,
                                        animals: event.animals,
                                        patternNumber: event.patternNumber,
                                        weekPattern: event.weekPattern,
                                        backgroundColor: Library.getColorFromPriority(event.priority),
                                    });
                                    //console.log('event racine :', dayObject.format('YYYY-MM-DD HH:mm:ss'));
                                }
                                else if (dayObject.isAfter(eventStart)) {
                                    calendar.addEvent({
                                        title: `C: ${event.name}`,
                                        start: dayObject.format('YYYY-MM-DD HH:mm:ss'),
                                        end: eventEnd.format('YYYY-MM-DD HH:mm:ss'),
                                        id: event.id,
                                        comment: event.comment,
                                        priority: event.priority,
                                        status: event.status,
                                        category: event.category,
                                        animals: event.animals,
                                        patternNumber: event.patternNumber,
                                        weekPattern: event.weekPattern,
                                        backgroundColor: Library.getColorFromPriority(event.priority),
                                    });
                                    //console.log('event non racine :', dayObject.format('YYYY-MM-DD HH:mm:ss'));
                                }
                            }
                        }
                    }
                }
            });



            // Afficher un popup avec les détails de l'événement
            calendar.setOption('eventClick',(info) => {
                //console.log('animals', info.event.extendedProps.animals);
                // Afficher un popup avec les détails de l'événement
        
                Swal.fire({
                    title: `${info.event.title}`,
                    html: `
                        <table id="table-alert-event">
                            <tr class="row-table-alert-event">
                                <td class="text-orange">Commentaire :</td>
                                <td>${info.event.extendedProps.comment}</td>
                            </tr>
                            <tr class="row-table-alert-event">
                                <td class="text-orange">Début :</td>
                                <td>${this.formatDateFct(info.event.start)}</td>
                            </tr>
                            <tr class="row-table-alert-event">
                                <td class="text-orange">Fin :</td>
                                <td>${this.formatDateFct(info.event.end)}</td>
                            </tr>
                            <tr class="row-table-alert-event">
                                <td class="text-orange">Priorité :</td>
                             <td><span class="icon-priority-alert-event bg-${Library.getColorFromPriority(info.event.extendedProps.priority)}"></span>  ${info.event.extendedProps.priority}</td>
                            </tr>
                            <tr class="row-table-alert-event">
                                <td class="text-orange">Statut :</td>
                                <td>${info.event.extendedProps.status}</td>
                            </tr>
                            <tr class="row-table-alert-event">
                                <td class="text-orange">Catégorie :</td>
                                <td>${info.event.extendedProps.category}</td>
                            </tr>
                            <tr class="row-table-alert-event">
                                <td class="text-orange">Animaux :</td>
                                <td>${this.formatAnimalListFct(info.event.extendedProps.animals)}</td>
                            </tr>
                        </table>
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
        
                const alertIconElement = document.getElementsByClassName('swal2-icon-content')[0];
                alertIconElement.style.color = Library.getColorFromPriority(info.event.extendedProps.priority);
            });
            calendar.render();
        });
    }


// Fonction pour formater la date
formatDateFct = function(date) {
    const options = {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
        timeZoneName: 'short'
    };

    return new Intl.DateTimeFormat('fr-FR', options).format(date);
}

// Fonction pour formater la liste d'animaux avec des balises <ul> et <li>
formatAnimalListFct = function(animals) {
    if (animals && animals.length > 0) {
        const animalListItems = animals.map(animal => `<div>${animal}</div>`).join('');
        return `<div>${animalListItems}</div>`;
    } else {
        return '<div>Aucun animal spécifié</div>';
    }
}
}
