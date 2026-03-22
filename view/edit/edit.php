<?php echo template::formOpen('blogEditForm'); ?>
<div class="row">
	<div class="col1">
		<?php echo template::button('blogEditBack', [
			'class' => 'buttonGrey',
			'href' => helper::baseUrl() . $this->getUrl(0) . '/config',
			'value' => template::ico('left')
		]); ?>
	</div>
	<div class="col3 offset6">
		<?php echo template::button('blogEditDraft', [
			'uniqueSubmission' => true,
			'value' => 'Brouillon'
		]); ?>
		<?php echo template::hidden('blogEditState', [
			'value' => true
		]); ?>
	</div>
	<div class="col2">
		<?php echo template::submit('blogEditSubmit', [
			'value' => 'Publier',
			'uniqueSubmission' => true
		]); ?>
	</div>
</div>
<div class="row">
	<div class="col12">
		<div class="block">
			<h4><?php echo helper::translate('Paramètres'); ?></h4>
			<?php $blogCategories = ['' => 'Sans catégorie']; ?>
			<?php foreach ((array) $this->getData(['module', $this->getUrl(0), 'config', 'categories']) as $category): ?>
				<?php $category = trim(preg_replace('/\s+/u', ' ', html_entity_decode((string) $category, ENT_QUOTES | ENT_HTML5, 'UTF-8'))); if ($category === '') { continue; } ?>
				<?php $blogCategories[$category] = $category; ?>
			<?php endforeach; ?>
			<?php $currentCategory = trim((string) $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'category'])); ?>
			<?php if ($currentCategory !== '' && !isset($blogCategories[$currentCategory])) { $blogCategories[$currentCategory] = $currentCategory; } ?>
			<?php
			$currentTags = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'tags']);
			if (is_array($currentTags)) {
				$currentTags = implode(', ', $currentTags);
			} else {
				$currentTags = trim((string) $currentTags);
			}
			?>
			<div class="row">
				<div class="col4">
					<?php echo template::text('blogEditTitle', [
						'label' => 'Titre',
						'value' => trim(preg_replace('/\s+/u', ' ', html_entity_decode((string) $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'title']), ENT_QUOTES | ENT_HTML5, 'UTF-8')))
					]); ?>
				</div>
				<div class="col4">
					<?php echo template::text('blogEditPermalink', [
						'label' => 'Permalink',
						'value' => $this->getUrl(2)
					]); ?>
				</div>
				<div class="col4">
					<?php echo template::select('blogEditCategory', $blogCategories, [
						'label' => 'Catégorie',
						'selected' => $currentCategory,
						'help' => count($blogCategories) === 1 ? "Définissez d'abord vos catégories dans la configuration du module." : ''
					]); ?>
				</div>
			</div>
			<div class="row">
				<div class="col12">
					<?php echo template::text('blogEditTags', [
						'label' => 'Tags',
						'value' => $currentTags,
						'help' => 'Jusqu’à 5 tags, séparés par des virgules. Le signe # est facultatif.'
					]); ?>
				</div>
			</div>
			<div class="row">
				<div class="col6">
					<?php echo template::file('blogEditPicture', [
						'language' => $this->getData(['user', $this->getUser('id'), 'language']),
						'help' => $this->getData(['theme', 'site', 'width']) !== '100%' ? 'Taille optimale de l\'image de couverture : ' . ((int) substr($this->getData(['theme', 'site', 'width']), 0, -2) - (20 * 2)) . ' x 350 pixels.' : '',
						'label' => 'Image de couverture',
						'type' => 1,
						'value' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'picture']),
						'folder' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'picture']) ? dirname($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'picture'])) : ''
					]); ?>
				</div>
				<div class="col3">
					<?php echo template::select('blogEditPictureSize', blog::$pictureSizes, [
						'label' => 'Largeur de l\'image',
						'selected' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'pictureSize'])
					]); ?>
				</div>
				<div class="col3">
					<?php echo template::select('blogEditPicturePosition', blog::$picturePositions, [
						'label' => 'Position',
						'selected' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'picturePosition']),
						'help' => 'Le texte de l\'article est adapté autour de l\'image'
					]); ?>
				</div>
			</div>
			<div class="row">
				<div class="col6">
					<?php echo template::checkbox('blogEditHidePicture', true, 'Masquer l\'image de couverture dans l\'article', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'hidePicture'])
					]); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo template::textarea('blogEditContent', [
	'class' => 'editorWysiwyg',
	'value' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'content'])
]); ?>
<div class="row">
	<div class="col12">
		<div class="block">
			<h4><?php echo helper::translate('Options de publication'); ?></h4>
			<div class="row">
				<div class="col4">
					<?php echo template::select('blogEditUserId', blog::$users, [
						'label' => 'Auteur',
						'selected' => $this->getUser('id'),
						'disabled' => $this->getUser('role') !== self::ROLE_ADMIN ? true : false
					]); ?>
				</div>
				<div class="col4">
					<?php echo template::date('blogEditPublishedOn', [
						'help' => 'L\'article n\'est visible qu\'après la date de publication prévue.',
						'type' => 'datetime-local',
						'label' => 'Publication',
						'value' => floor($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'publishedOn']) / 60) * 60
					]); ?>
				</div>
				<div class="col4">
					<?php echo template::select('blogEditConsent', blog::$articleConsent, [
						'label' => 'Édition - Suppression',
						'selected' => is_numeric($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'editConsent'])) ? blog::EDIT_ROLE : $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'editConsent']),
						'help' => 'Les utilisateurs des rôles supérieurs accèdent à l\'article sans restriction'
					]); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col12">
		<div class="block">
			<h4><?php echo helper::translate('Commentaires'); ?></h4>
			<div class="row">
				<div class="col4 ">
					<?php echo template::checkbox('blogEditCommentClose', true, 'Fermer les commentaires', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentClose'])
					]); ?>
				</div>
				<div class="col4 commentOptionsWrapper ">
					<?php echo template::checkbox('blogEditCommentApproved', true, 'Approbation par un modérateur', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentApproved']),
						''
					]); ?>
				</div>
				<div class="col4 commentOptionsWrapper">
					<?php echo template::select('blogEditCommentMaxlength', blog::$commentsLength, [
						'help' => 'Choix du nombre maximum de caractères pour chaque commentaire de l\'article, mise en forme html comprise.',
						'label' => 'Caractères par commentaire',
						'selected' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentMaxlength'])
					]); ?>
				</div>

			</div>
			<div class="row">
				<div class="col3 commentOptionsWrapper offset2">
					<?php echo template::checkbox('blogEditCommentNotification', true, 'Notification par email', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentNotification']),
					]); ?>
				</div>
				<div class="col4 commentOptionsWrapper">
					<?php echo template::select('blogEditCommentGroupNotification', blog::$roleNews, [
						'selected' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentGroupNotification']),
					]); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo template::formClose(); ?>
