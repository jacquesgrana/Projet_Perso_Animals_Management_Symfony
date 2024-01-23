// The goal of this language config is to give FullCalendar everything it needs for
// translations (in this case, French). This happens to be a merge of Moment's and
// the jQuery UI datepicker's configs, in addition to some other strings.
//
// Additionally, when this config is loaded, Moment and the jQuery UI datepicker
// (if it is on the page) will also be initialized.
//
(function(){
    function onload (moment, $) {

        // RIPPED STRAIGHT FROM MOMENT'S SOURCE
        // (https://github.com/moment/moment/blob/develop/lang/fr.js)
        //
        moment.lang('fr', {
            months : "janvier_février_mars_avril_mai_juin_juillet_août_septembre_octobre_novembre_décembre".split("_"),
            monthsShort : "janv._févr._mars_avr._mai_juin_juil._août_sept._oct._nov._déc.".split("_"),
            weekdays : "dimanche_lundi_mardi_mercredi_jeudi_vendredi_samedi".split("_"),
            weekdaysShort : "dim._lun._mar._mer._jeu._ven._sam.".split("_"),
            weekdaysMin : "Di_Lu_Ma_Me_Je_Ve_Sa".split("_"),
            longDateFormat : {
                LT : "HH:mm",
                L : "DD/MM/YYYY",
                LL : "D MMMM YYYY",
                LLL : "D MMMM YYYY LT",
                LLLL : "dddd D MMMM YYYY LT"
            },
            calendar : {
                sameDay: "[Aujourd'hui à] LT",
                nextDay: '[Demain à] LT',
                nextWeek: 'dddd [à] LT',
                lastDay: '[Hier à] LT',
                lastWeek: 'dddd [dernier à] LT',
                sameElse: 'L'
            },
            relativeTime : {
                future : "dans %s",
                past : "il y a %s",
                s : "quelques secondes",
                m : "une minute",
                mm : "%d minutes",
                h : "une heure",
                hh : "%d heures",
                d : "un jour",
                dd : "%d jours",
                M : "un mois",
                MM : "%d mois",
                y : "un an",
                yy : "%d ans"
            },
            ordinal : function (number) {
                return number + (number === 1 ? 'er' : '');
            },
            week : {
                dow : 1, // Monday is the first day of the week.
                doy : 4  // The week that contains Jan 4th is the first week of the year.
            }
        });
        
        
        if ($.fullCalendar) {
            $.fullCalendar.lang('fr', {
                // strings we need that are neither in Moment nor datepicker
                "day": "Jour",
                "week": "Semaine",
                "month": "Mois",
                "list": "Mon planning"
            }, {
                // VALUES ARE FROM JQUERY-UI DATEPICKER'S TRANSLATIONS
                // (https://github.com/jquery/jquery-ui/blob/master/ui/i18n/jquery.ui.datepicker-fr.js)
                // 
                // Values that are reduntant because of Moment are not included here.
                //
                // When fullCalendar's lang method is called, it will merge this config with Moment's
                // and make this stuff available for jQuery UI's datepicker:
                //
                //     $.datepicker.regional['fr'] = ...
                //
                closeText: 'Fermer',
                prevText: 'Précédent',
                nextText: 'Suivant',
                currentText: 'Aujourd\'hui',
                dayNamesMin: ['D','L','M','M','J','V','S'],
                weekHeader: 'Sem.',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            });
        }

    }

    // we need Moment and jQuery before we can begin...
    //
    if (typeof define === "function" && define.amd) {
        define(["moment", "jquery"], onload);
    }
    if (typeof window !== "undefined" && window.moment && window.jQuery) {
        onload(window.moment, window.jQuery);
    }

})();
