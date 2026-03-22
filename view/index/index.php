<?php
$blogBaseUrl = helper::baseUrl() . $this->getUrl(0);
$blogSafeText = function ($text) {
	return htmlspecialchars(
		trim(preg_replace('/\s+/u', ' ', html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8'))),
		ENT_QUOTES,
		'UTF-8'
	);
};
?>
<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'feeds'])): ?>
	<div id="rssFeed">
		<a type="application/rss+xml" href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/rss'; ?>" target="_blank">
			<img src='module/blog/ressource/feed-icon-16.gif' />
			<?php echo $this->getData(['module', $this->getUrl(0), 'config', 'feedsLabel']) ? '<p>' . $this->getData(['module', $this->getUrl(0), 'config', 'feedsLabel']) . '</p>' : ''; ?>
		</a>
	</div>
<?php endif; ?>

<div class="blogSortRow">
	<details class="blogSortDock" id="blogSortDock">
		<summary class="blogSortToggle" aria-label="Trier les articles" title="Trier les articles">
			<svg viewBox="0 0 24 24" width="14" height="14" focusable="false" aria-hidden="true"><path d="M8 10l4 5l4-5z"/></svg>
		</summary>
		<div class="blogSortPanel">
			<?php foreach (blog::$sortOptions as $sortKey => $sortLabel): ?>
				<a
					class="blogSortLink<?php echo blog::$currentSortBy === $sortKey ? ' is-active' : ''; ?>"
					data-sort="<?php echo $sortKey; ?>"
					href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/sort/' . rawurlencode($sortKey) . '#article'; ?>"
				>
					<?php echo $blogSafeText($sortLabel); ?>
				</a>
			<?php endforeach; ?>
		</div>
	</details>
</div>

<?php if (blog::$articles): ?>
	<div id="blogArticleList">
	<?php foreach (blog::$articles as $articleId => $article): ?>
		<?php
		$articleTags = [];
		foreach ((array) ($article['tags'] ?? []) as $tag) {
			$tag = trim(preg_replace('/\s+/u', ' ', html_entity_decode((string) $tag, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
			if ($tag !== '') {
				$articleTags[] = $tag;
			}
		}
		$sortTitle    = htmlspecialchars(mb_strtolower(trim(strip_tags($article['title'] ?? '')), 'UTF-8'), ENT_QUOTES, 'UTF-8');
		$sortCategory = htmlspecialchars(mb_strtolower(trim(strip_tags($article['category'] ?? '')), 'UTF-8'), ENT_QUOTES, 'UTF-8');
		$sortDate     = (int) ($article['publishedOn'] ?? 0);
		?>
		<article
			id="article"
			class="blogCard"
			data-sort-title="<?php echo $sortTitle; ?>"
			data-sort-category="<?php echo $sortCategory; ?>"
			data-sort-date="<?php echo $sortDate; ?>"
		>
			<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'layout']) === true): ?>
				<div class="row">
					<div class="col12">
						<h2 class="blogTitle">
							<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId . blog::$indexQueryString; ?>">
								<?php echo $blogSafeText($article['title']); ?>
							</a>
						</h2>
						<div class="blogMetaCluster">
							<?php if (!empty($article['category'])): ?>
								<span class="blogCategoryBadge"><?php echo $blogSafeText($article['category']); ?></span>
							<?php endif; ?>
							<?php foreach ($articleTags as $tag): ?>
								<span class="blogTagBadge">#<?php echo $blogSafeText($tag); ?></span>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col6 blogEdit">
						<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showPseudo']) === true): ?>
							<?php echo template::ico('user'); ?>
							<?php echo $this->signature($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'userId'])); ?>
						<?php endif; ?>
						<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true || $this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true): ?>
							<?php echo template::ico('calendar-empty', ['margin' => 'left']); ?>
						<?php endif; ?>
						<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true): ?>
							<?php echo helper::dateUTF8(blog::$dateFormat, $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'publishedOn']), self::$i18nUI); ?>
						<?php endif; ?>
						<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true && $this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true): ?>
							<?php echo '&nbsp;—&nbsp;'; ?>
						<?php endif; ?>
						<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true): ?>
							<?php echo helper::dateUTF8(blog::$timeFormat, $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'publishedOn']), self::$i18nUI); ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="row">
					<div class="col12">
						<?php if ($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'picture']) && file_exists($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'picture']))): ?>
							<?php $pictureSize = $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'pictureSize']) === null ? '100' : $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'pictureSize']); ?>
							<?php if ($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'hidePicture']) == false) {
								echo '<img class="blogArticlePicture blogArticlePicture' . $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'picturePosition']) .
									' pict' . $pictureSize . '" src="' . $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'picture']) .
									'" alt="' . basename($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'picture'])) . '">';
							} ?>
						<?php endif; ?>

						<?php $lenght = (int) $this->getData(['module', $this->getUrl(0), 'config', 'articlesLenght']); ?>
						<?php if ($lenght > 0): ?>
							<?php echo helper::subword($article['content'], 0, $lenght); ?>...
							<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId . blog::$indexQueryString; ?>">
								<button class="readMoreButton"><?php echo helper::translate('Lire la suite'); ?></button>
							</a>
						<?php else: ?>
							<?php echo $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'content']); ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="row">
					<div class="col6 blogEdit">
						<?php if ($this->isConnected() === true and ((($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'editConsent']) === blog::EDIT_OWNER and ($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'userId']) === $this->getUser('id') or $this->getUser('role') === self::ROLE_ADMIN)) ) or ((($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'editConsent']) === self::ROLE_ADMIN or $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'editConsent']) === self::ROLE_EDITOR) and $this->getUser('role') >= $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'editConsent']))) or (($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'editConsent']) === blog::EDIT_ALL and $this->getUser('role') >= blog::$actions['config'])))): ?>
							<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/edit/' . $articleId; ?>">
								<?php echo template::ico('pencil'); ?> Éditer
							</a>
						<?php endif; ?>
					</div>
					<div class="col6 textAlignRight" id="comment">
						<?php if ($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'commentClose'])): ?>
							<p>Cet article ne reçoit pas de commentaire.</p>
						<?php else: ?>
							<p>
								<?php echo template::ico('comment', ['margin' => 'right']); ?>
								<?php if ((int) (blog::$comments[$articleId] ?? 0) > 0): ?>
									<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId . blog::$indexQueryString; ?>">
										<?php echo blog::$comments[$articleId]; ?> commentaire<?php echo blog::$comments[$articleId] > 1 ? 's' : ''; ?>
									</a>
								<?php else: ?>
									Pas encore de commentaire
								<?php endif; ?>
							</p>
						<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
				<div class="row">
					<?php if ($article['picture'] && file_exists($article['picture'])): ?>
						<div class="col3">
							<?php
								$picture = $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'picture']);
								$thumb = $this->getThumb($picture);
								if (file_exists($thumb) === false) {
									$this->makeThumb($picture, null, self::THUMBS_WIDTH);
								}
							?>
							<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId . blog::$indexQueryString; ?>" class="blogPicture">
								<img src="<?php echo $thumb; ?>" alt="<?php echo basename($article['picture']); ?>">
							</a>
						</div>
						<div class="col9">
					<?php else: ?>
						<div class="col12">
					<?php endif; ?>
						<h2 class="blogTitle">
							<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId . blog::$indexQueryString; ?>">
								<?php echo $blogSafeText($article['title']); ?>
							</a>
						</h2>
						<div class="blogMetaCluster">
							<?php if (!empty($article['category'])): ?>
								<span class="blogCategoryBadge"><?php echo $blogSafeText($article['category']); ?></span>
							<?php endif; ?>
							<?php foreach ($articleTags as $tag): ?>
								<span class="blogTagBadge">#<?php echo $blogSafeText($tag); ?></span>
							<?php endforeach; ?>
						</div>
						<div class="blogComment">
							<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId . blog::$indexQueryString; ?>#comment">
								<?php if (blog::$comments[$articleId]): ?>
									<?php echo blog::$comments[$articleId]; ?>
									<?php echo template::ico('comment', ['margin' => 'left']); ?>
								<?php endif; ?>
							</a>
						</div>
						<div class="blogDate">
							<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showPseudo']) === true): ?>
								<?php echo template::ico('user'); ?>
								<?php echo $this->signature($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'userId'])); ?>
							<?php endif; ?>
							<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true || $this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true): ?>
								<?php echo template::ico('calendar-empty', ['margin' => 'left']); ?>
							<?php endif; ?>
							<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true): ?>
								<?php echo helper::dateUTF8(blog::$dateFormat, $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'publishedOn']), self::$i18nUI); ?>
							<?php endif; ?>
							<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true && $this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true): ?>
								<?php echo '&nbsp;—&nbsp;'; ?>
							<?php endif; ?>
							<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true): ?>
								<?php echo helper::dateUTF8(blog::$timeFormat, $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'publishedOn']), self::$i18nUI); ?>
							<?php endif; ?>
						</div>
						<div class="blogContent">
							<?php $lenght = (int) $this->getData(['module', $this->getUrl(0), 'config', 'articlesLenght']); ?>
							<?php if ($lenght > 0): ?>
								<?php echo helper::subword($article['content'], 0, $lenght); ?>...
								<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId . blog::$indexQueryString; ?>">
									<button class="readMoreButton"><?php echo helper::translate('Lire la suite'); ?></button>
								</a>
							<?php else: ?>
								<?php echo $article['content']; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</article>
	<?php endforeach; ?>
	</div>
	<?php echo blog::$pages; ?>
<?php else: ?>
	<?php echo template::speech('Aucun article publié.'); ?>
<?php endif; ?>

