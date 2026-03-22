<?php echo template::formOpen('blogConfig'); ?>
	<div class="row">
		<div class="col1">
			<?php echo template::button('blogConfigBack', [
				'class' => 'buttonGrey',
				'href' => helper::baseUrl() . 'page/edit/' . $this->getUrl(0) . '/' . self::$siteContent,
				'value' => template::ico('left')
			]); ?>
		</div>
		<div class="col1 offset8">
			<?php echo template::button('blogConfigOption', [
				'href' => helper::baseUrl() . $this->getUrl(0) . '/option',
				'value' => template::ico('sliders'),
				'help' => 'Options de configuration'
			]); ?>
		</div>
		<div class="col1">
			<?php echo template::button('blogConfigAdd', [
				'href' => helper::baseUrl() . $this->getUrl(0) . '/add',
				'value' => template::ico('plus'),
				'class' => 'buttonGreen',
				'help' => 'Rédiger un article'
			]); ?>
		</div>
		<div class="col1">
			<?php echo template::submit('blogConfigSubmit', [
				'value' => template::ico('check'),
				'help' => 'Enregistrer le tri par défaut et les catégories'
			]); ?>
		</div>
	</div>

	<div class="row">
		<div class="col12">
			<div class="block">
				<h4>Classement et taxonomie</h4>
				<div class="row">
					<div class="col4">
						<?php echo template::select('blogConfigSortBy', blog::$sortOptions, [
							'label' => 'Tri par défaut',
							'selected' => $this->getData(['module', $this->getUrl(0), 'config', 'sortBy'])
						]); ?>
					</div>
					<div class="col8">
						<?php echo template::textarea('blogConfigCategories', [
							'label' => 'Catégories disponibles',
							'help' => 'Une catégorie par ligne ou séparées par des virgules. Elles seront proposées ensuite dans la rédaction de chaque article.',
							'value' => implode(PHP_EOL, array_map(function ($category) {
								return trim(preg_replace('/\s+/u', ' ', html_entity_decode((string) $category, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
							}, (array) $this->getData(['module', $this->getUrl(0), 'config', 'categories'])))
						]); ?>
					</div>
				</div>
				<p style="margin:.6rem 0 0 0; opacity:.82;">Chaque article peut ensuite recevoir une catégorie unique et jusqu’à cinq tags.</p>
			</div>
		</div>
	</div>
<?php echo template::formClose(); ?>
<?php if(blog::$articles): ?>
	<?php echo template::table([4, 4, 1, 1, 1, 1], blog::$articles, ['Titre', 'Publication', 'État', 'Commentaires', '', '']); ?>
	<?php echo blog::$pages; ?>
<?php else: ?>
	<?php echo template::speech('Aucun article'); ?>
<?php endif; ?>
<div class="moduleVersion">Version n°
	<?php echo blog::VERSION; ?>
</div>
