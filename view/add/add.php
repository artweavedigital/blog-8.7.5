<?php echo template::formOpen('blogAddForm'); ?>
	<div class="row">
		<div class="col1">
			<?php echo template::button('blogAddBack', [
				'class' => 'buttonGrey',
				'href' => helper::baseUrl() . $this->getUrl(0) . '/config',
				'value' => template::ico('left')
			]); ?>
		</div>
		<div class="col2 offset7">
			<?php echo template::button('blogAddDraft', [
				'uniqueSubmission' => true,
				'value' => 'Brouillon'
			]); ?>
			<?php echo template::hidden('blogAddState', [
				'value' => true
			]); ?>
		</div>
		<div class="col2">
			<?php echo template::submit('blogAddPublish', [
				'value' => 'Publier',
				'uniqueSubmission' => true
			]); ?>
		</div>
	</div>
	<div class="row">
		<div class="col12">
			<div class="block">
				<h4><?php echo helper::translate('Informations générales');?></h4>
				<?php $blogCategories = ['' => 'Sans catégorie']; ?>
				<?php foreach ((array) $this->getData(['module', $this->getUrl(0), 'config', 'categories']) as $category): ?>
					<?php $category = trim(preg_replace('/\s+/u', ' ', html_entity_decode((string) $category, ENT_QUOTES | ENT_HTML5, 'UTF-8'))); if ($category === '') { continue; } ?>
					<?php $blogCategories[$category] = $category; ?>
				<?php endforeach; ?>
				<div class="row">
					<div class="col4">
						<?php echo template::text('blogAddTitle', [
							'label' => 'Titre'
						]); ?>
					</div>
					<div class="col4">
						<?php echo template::text('blogAddPermalink', [
							'label' => 'Permalink'
						]); ?>
					</div>
					<div class="col4">
						<?php echo template::select('blogAddCategory', $blogCategories, [
							'label' => 'Catégorie',
							'help' => count($blogCategories) === 1 ? "Définissez d'abord vos catégories dans la configuration du module." : ""
						]); ?>
					</div>
				</div>
				<div class="row">
					<div class="col12">
						<?php echo template::text('blogAddTags', [
							'label' => 'Tags',
							'help' => 'Jusqu’à 5 tags, séparés par des virgules. Le signe # est facultatif.'
						]); ?>
					</div>
				</div>
				<div class="row">
					<div class="col4">
						<?php echo template::file('blogAddPicture', [
							'language' => $this->getData(['user', $this->getUser('id'), 'language']),
							'help' => 'Taille optimale de l\'image de couverture : ' . ((int) substr($this->getData(['theme', 'site', 'width']), 0, -2) - (20 * 2)) . ' x 350 pixels.',
							'label' => 'Image de couverture',
							'type' => 1
						]); ?>
					</div>
					<div class="col4">
						<?php echo template::select('blogAddPictureSize', blog::$pictureSizes, [
							'label' => 'Largeur de l\'image'
						]); ?>
					</div>
					<div class="col4">
						<?php echo template::select('blogAddPicturePosition', blog::$picturePositions, [
							'label' => 'Position',
							'help' => 'Le texte de l\'article est adapté autour de l\'image'
						]); ?>
					</div>
				</div>
				<div class="row">
					<div class="col6">
						<?php echo template::checkbox('blogAddHidePicture', true, 'Masquer l\'image de couverture dans l\'article', [
							'checked' => true
						]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo template::textarea('blogAddContent', [
		'class' => 'editorWysiwyg'
	]); ?>
	<div class="row">
		<div class="col12">
			<div class="block">
				<h4><?php echo helper::translate('Options de publication'); ?></h4>
				<div class="row">
					<div class="col4">
						<?php echo template::select('blogAddUserId', blog::$users, [
							'label' => 'Auteur',
							'selected' => $this->getUser('id'),
							'disabled' => $this->getUser('role') !== self::ROLE_ADMIN ? true : false
						]); ?>
					</div>
					<div class="col4">
						<?php echo template::date('blogAddPublishedOn', [
							'help' => 'L\'article n\'est visible qu\'après la date de publication prévue.',
							'label' => 'Date de publication',
							'type' => 'datetime-local',
							'format' => $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']) . $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']),
							'value' => floor(strtotime('now') / 60) * 60
						]); ?>
					</div>
					<div class="col4">
						<?php echo template::select('blogAddConsent', blog::$articleConsent  , [
							'label' => 'Édition - Suppression',
							'selected' => blog::EDIT_ALL,
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
				<h4><?php echo helper::translate('Commentaires');?></h4>
				<div class="row">
					<div class="col4 ">
						<?php echo template::checkbox('blogAddCommentClose', true, 'Fermer les commentaires'); ?>
					</div>
					<div class="col4 commentOptionsWrapper ">
						<?php echo template::checkbox('blogAddCommentApproved', true, 'Approbation par un modérateur'); ?>
					</div>
					<div class="col4 commentOptionsWrapper">
						<?php echo template::select('blogAddCommentMaxlength', blog::$commentsLength,[
							'help' => 'Choix du nombre maximum de caractères pour chaque commentaire de l\'article, mise en forme html comprise.',
							'label' => 'Caractères par commentaire'
						]); ?>
					</div>
				</div>
				<div class="row">
					<div class="col3 commentOptionsWrapper offset2">
						<?php echo template::checkbox('blogAddCommentNotification', true, 'Notification par email'); ?>
					</div>
					<div class="col4 commentOptionsWrapper">
						<?php echo template::select('blogAddCommentGroupNotification', blog::$roleNews); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php echo template::formClose(); ?>
