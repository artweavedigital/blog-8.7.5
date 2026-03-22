/**
 * This file is part of Zwii.
 *
 * For full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 *
 * @author Rémi Jean <remi.jean@outlook.com>
 * @copyright Copyright (C) 2008-2018, Rémi Jean
 * @author Frédéric Tempez <frederic.tempez@outlook.com>
 * @copyright Copyright (C) 2018-2025, Frédéric Tempez
 * @license CC Attribution-NonCommercial-NoDerivatives 4.0 International 
 * @Copyright (C) 2026, Frédéric Tempez
 * @Licensed under the GNU General Public License v3.0 or later.
 * @link http://zwiicms.fr/
 */

$(document).ready(function() {
    // Gestion du changement de la case "Afficher la date"
    $('#blogOptionShowDate').change(function() {
        var showDateChecked = $(this).is(':checked');
        
        // Afficher ou masquer le wrapper de l'heure selon l'état de la date
        if (showDateChecked) {
            $('.timeWrapper').show();
        } else {
            $('.timeWrapper').hide();
            // Désactiver l'option "Afficher l'heure" lorsque la date est désactivée
            $('#blogOptionShowTime').prop('checked', false).trigger('change');
        }
        
        // Afficher ou masquer le format de la date
        $('#blogOptionDateFormatWrapper').toggle(showDateChecked);
    }).trigger('change'); // Déclenchement au chargement de la page

    // Gestion du changement de la case "Afficher l'heure"
    $('#blogOptionShowTime').change(function() {
        var showTimeChecked = $(this).is(':checked');
        
        // Afficher ou masquer le format de l'heure
        $('#blogOptionTimeFormatWrapper').toggle(showTimeChecked);
    }).trigger('change'); // Déclenchement au chargement de la page
});
