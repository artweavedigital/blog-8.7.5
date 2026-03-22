/**
 * Navigation serveur pour le tri public du blog.
 * Aucun tri client n'intercepte le clic afin de garantir la cohérence
 * avec la pagination et le retour depuis un article.
 */
$(document).ready(function () {
	$('.blogSortLink').on('click', function () {
		$('.blogSortLink').removeClass('is-active');
		$(this).addClass('is-active');
	});
});
