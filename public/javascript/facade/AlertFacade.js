class AlertFacade {
    
    static displayAndGetConfirm = async (text) => {
        const toReturn = await Swal.fire({
            title: 'Confirmation',
            text: text,
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
        return toReturn;
    }

    static displayAlert = async (text, icon) => {
        const toReturn = await Swal.fire({
            title: 'Information',
            text: text,
            icon: icon, //'info'
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
    }
}