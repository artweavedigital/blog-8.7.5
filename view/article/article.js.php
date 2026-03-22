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
 * Affiche le bloc pour rédiger un commentaire
 */
var commentShowDOM = $("#blogArticleCommentShow");
commentShowDOM.on("click focus", function() {
	$("#blogArticleCommentShowWrapper").fadeOut(function() {
		$("#blogArticleCommentWrapper").fadeIn();
		$("#blogArticleCommentContent").trigger("focus");
	});
});
if($("#blogArticleCommentWrapper").find("textarea.notice,input.notice").length) {
	commentShowDOM.trigger("click");
}

/**
 * Cache le bloc pour rédiger un commentaire
 */
$("#blogArticleCommentHide").on("click focus", function() {
	$("#blogArticleCommentWrapper").fadeOut(function() {
		$("#blogArticleCommentShowWrapper").fadeIn();
		$("#blogArticleCommentContent").val("");
		$("#blogArticleCommentAuthor").val("");
	});
});

/**
 * Force le scroll vers les commentaires en cas d'erreur
 */
$("#blogArticleCommentForm").on("submit", function() {
	$(location).attr("href", "#comment");
});