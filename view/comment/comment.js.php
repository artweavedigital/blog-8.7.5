/**
 * This file is part of Zwii.
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


/**
 * Confirmation de suppression
 */
$(".blogCommentDelete").on("click", function() {
	var _this = $(this);
	var message = "<?php echo helper::translate('Supprimer le commentaire ?'); ?>";
	return core.confirm(message , function() {
		$(location).attr("href", _this.attr("href"));
	});
});

/**
 * Confirmation d'approbation
 */
$(".blogCommentApproved").on("click", function() {
	var _this = $(this);
	var message = "<?php echo helper::translate('Approuver le commentaire ?'); ?>";
	return core.confirm(message , function() {
		$(location).attr("href", _this.attr("href"));
	});
});

/**
 * Confirmation de rejet
 */
$(".blogCommentRejected").on("click", function() {
	var _this = $(this);
	var message = "<?php echo helper::translate('Rejeter le commentaire ?'); ?>";
	return core.confirm(message , function() {
		$(location).attr("href", _this.attr("href"));
	});
});

/**
 * Confirmation de suppression en masse
 */
$(".blogCommentDeleteAll").on("click", function() {
	var _this = $(this);
	if( nombre === "1"){
		var message = "<?php echo helper::translate('Supprimer le commentaire ?'); ?>";
	} else {
		var message = "<?php echo helper::translate('Supprimer tous les commentairess ?'); ?>";
	}
	return core.confirm(message, function() {
		$(location).attr("href", _this.attr("href"));
	});
});
