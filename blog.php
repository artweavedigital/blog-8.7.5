<?php

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

class blog extends common
{

	const VERSION = '8.7.5';
	const REALNAME = 'Blog';
	const DELETE = true;
	const UPDATE = '0.0';
	const DATADIRECTORY = ''; // Contenu localisé inclus par défaut (page.json et module.json)
	const TAGS_LIMIT = 5;

	const EDIT_OWNER = 'owner';
	const EDIT_ROLE = 'role';
	const EDIT_ALL = 'all';

	public static $actions = [
		'add' => self::ROLE_EDITOR,
		'comment' => self::ROLE_EDITOR,
		'commentApprove' => self::ROLE_EDITOR,
		'commentDelete' => self::ROLE_EDITOR,
		'commentDeleteAll' => self::ROLE_EDITOR,
		'config' => self::ROLE_EDITOR,
		'option' => self::ROLE_EDITOR,
		'delete' => self::ROLE_EDITOR,
		'edit' => self::ROLE_EDITOR,
		'index' => self::ROLE_VISITOR,
		'sort' => self::ROLE_VISITOR,
		'rss' => self::ROLE_VISITOR,
		'pdf' => self::ROLE_VISITOR,
		'md' => self::ROLE_VISITOR,
		'epub' => self::ROLE_VISITOR
	];

	public static $articles = [];

	public static $categories = [];

	public static $currentSortBy = 'publishedOn_desc';

	public static $currentCategory = '';

	public static $currentSearch = '';

	public static $indexQueryString = '';

	public static $searchSuggestions = [];

	public static $searchFacets = [
		'categories' => [],
		'authors' => [],
		'years' => [],
	];

	public static $searchFilters = [
		'category' => '',
		'author' => '',
		'year' => '',
	];

	public static $searchResultsCount = 0;

	public static $sortOptions = [
		'publishedOn_desc' => 'Date (récent → ancien)',
		'publishedOn_asc' => 'Date (ancien → récent)',
		'title_asc' => 'Titre (A → Z)',
		'title_desc' => 'Titre (Z → A)',
		'category_asc' => 'Catégorie (A → Z)',
		'category_desc' => 'Catégorie (Z → A)',
	];

	// Signature du commentaire
	public static $editCommentSignature = '';

	public static $comments = [];

	public static $nbCommentsApproved = 0;

	public static $commentsDelete;

	// Signatures des commentaires déjà saisis
	public static $commentsSignature = [];

	public static $pages;

	public static $states = [
		false => 'Brouillon',
		true => 'Publié'
	];

	public static $pictureSizes = [
		'20' => 'Très petite',
		'30' => 'Petite',
		'40' => 'Grande',
		'50' => 'Très Grande',
		'100' => 'Pleine largeur',
	];

	public static $picturePositions = [
		'left' => 'À gauche',
		'right' => 'À droite ',
	];

	// Nombre d'objets par page
	public static $ArticlesListed = [
		1 => '1 article',
		2 => '2 articles',
		4 => '4 articles',
		6 => '6 articles',
		8 => '8 articles',
		10 => '10 articles',
		12 => '12 articles'
	];

	//Paramètre longueur maximale des commentaires en nb de caractères
	public static $commentsLength = [
		100 => '100 signes',
		250 => '250 signes',
		500 => '500 signes',
		750 => '750 signes'
	];

	public static $articlesLenght = [
		0 => 'Articles complets',
		600 => '600 signes',
		800 => '800 signes',
		1000 => '1000 signes',
		1200 => '1200 signes',
		1400 => '1400 signes',
		1600 => '1600 signes',
		1800 => '1800 signes',
	];

	public static $articlesLayout = [
		false => 'Classique',
		true => 'Moderne',
	];

	// Permissions d'un article
	public static $articleConsent = [
		self::EDIT_ALL => 'Tous les rôles',
		self::EDIT_ROLE => 'Rôle du propriétaire',
		self::EDIT_OWNER => 'Propriétaire'
	];

	public static $dateFormats = [
		'%d %B %Y' => 'DD MMMM YYYY',
		'%d/%m/%Y' => 'DD/MM/YYYY',
		'%m/%d/%Y' => 'MM/DD/YYYY',
		'%d/%m/%y' => 'DD/MM/YY',
		'%m/%d/%y' => 'MM/DD/YY',
		'%d-%m-%Y' => 'DD-MM-YYYY',
		'%m-%d-%Y' => 'MM-DD-YYYY',
		'%d-%m-%y' => 'DD-MM-YY',
		'%m-%d-%y' => 'MM-DD-YY',
	];
	public static $timeFormats = [
		'%H:%M' => 'HH:MM',
		'%I:%M %p' => "HH:MM tt",
	];

	public static $timeFormat = '';
	public static $dateFormat = '';

	// Nombre d'articles dans la page de config:
	public static $itemsperPage = 8;


	public static $users = [];



	/**
	 * Mise à jour du module
	 * Appelée par les fonctions index et config
	 */
	private function update()
	{
		// Initialisation
		if (is_null($this->getData(['module', $this->getUrl(0), 'config', 'versionData']))) {
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '0.0']);
		}
		// Version 5.0
		if (version_compare($this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '5.0', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'itemsperPage', 6]);
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '5.0']);
		}
		// Version 6.0
		if (version_compare($this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '6.0', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'feeds', false]);
			$this->setData(['module', $this->getUrl(0), 'config', 'feedsLabel', '']);
			$this->setData(['module', $this->getUrl(0), 'config', 'articlesLenght', 0]);
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '6.0']);
		}
		// Version 6.5
		if (version_compare($this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '6.5', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'dateFormat', '%d %B %Y']);
			$this->setData(['module', $this->getUrl(0), 'config', 'timeFormat', '%H:%M']);
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '6.5']);
		}
		// Version 8.0
		if (version_compare($this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '8.0', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'buttonBack', true]);
			$this->setData(['module', $this->getUrl(0), 'config', 'showTime', true]);
			$this->setData(['module', $this->getUrl(0), 'config', 'showDate', true]);
			$this->setData(['module', $this->getUrl(0), 'config', 'showPseudo', true]);
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '8.0']);
		}
		// Version 8.7.0
		if (version_compare($this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '8.7.0', '<')) {
			if ($this->getData(['module', $this->getUrl(0), 'config', 'sortBy']) === null) {
				$this->setData(['module', $this->getUrl(0), 'config', 'sortBy', 'publishedOn_desc']);
			}
			if ($this->getData(['module', $this->getUrl(0), 'config', 'categories']) === null) {
				$this->setData(['module', $this->getUrl(0), 'config', 'categories', []]);
			}
			$posts = $this->getData(['module', $this->getUrl(0), 'posts']);
			if (is_array($posts)) {
				foreach ($posts as $postId => $post) {
					$this->setData(['module', $this->getUrl(0), 'posts', $postId, 'category', $this->normalizeCategoryLabel($post['category'] ?? '')]);
					$this->setData(['module', $this->getUrl(0), 'posts', $postId, 'tags', $this->normalizeTagsInput($post['tags'] ?? [])]);
				}
			}
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '8.7.0']);
		}
		if (version_compare((string) $this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '8.7.1', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '8.7.1']);
		}
		if (version_compare((string) $this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '8.7.5', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '8.7.5']);
		}
	}

	public function normalizeCategoryLabel($category)
	{
		$category = html_entity_decode((string) $category, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$category = preg_replace('/\s+/u', ' ', $category);
		return trim((string) $category);
	}

	public function decodeDisplayText($text)
	{
		$text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$text = preg_replace('/\s+/u', ' ', $text);
		return trim((string) $text);
	}

	public function escapeDisplayText($text)
	{
		return htmlspecialchars($this->decodeDisplayText($text), ENT_QUOTES, 'UTF-8');
	}

	private function normalizeCategoriesInput($input)
	{
		if (is_array($input)) {
			$categories = $input;
		} else {
			$categories = preg_split('/[,;\r\n]+/u', (string) $input);
		}

		$normalized = [];
		foreach ((array) $categories as $category) {
			$category = $this->normalizeCategoryLabel($category);
			if ($category === '') {
				continue;
			}
			$normalized[$category] = $category;
		}

		return array_values($normalized);
	}

	private function getConfiguredCategories()
	{
		return $this->normalizeCategoriesInput((array) $this->getData(['module', $this->getUrl(0), 'config', 'categories']));
	}

	private function getConfiguredSortBy()
	{
		$configSort = (string) $this->getData(['module', $this->getUrl(0), 'config', 'sortBy']);
		return array_key_exists($configSort, self::$sortOptions) ? $configSort : 'publishedOn_desc';
	}

	private function getRequestedSearchTerm()
	{
		$search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
		$search = strip_tags($search);
		return mb_substr($search, 0, 120);
	}

	private function getRequestedSearchFilter($name)
	{
		$map = [
			'category' => 'searchCategory',
			'author' => 'searchAuthor',
			'year' => 'searchYear',
		];

		if (!isset($map[$name])) {
			return '';
		}

		$value = isset($_GET[$map[$name]]) ? trim((string) $_GET[$map[$name]]) : '';
		$value = strip_tags($value);

		if ($name === 'year') {
			return preg_match('/^\d{4}$/', $value) ? $value : '';
		}

		return mb_substr($value, 0, 120);
	}

	private function getSessionSortStorageKey()
	{
		return 'ZWII_BLOG_VISITOR_SORT';
	}

	private function getStoredVisitorSortBy()
	{
		$pageId = (string) $this->getUrl(0);
		$storageKey = $this->getSessionSortStorageKey();
		$stored = isset($_SESSION[$storageKey][$pageId]) ? (string) $_SESSION[$storageKey][$pageId] : '';
		return array_key_exists($stored, self::$sortOptions) ? $stored : '';
	}

	private function setStoredVisitorSortBy($sortBy)
	{
		$pageId = (string) $this->getUrl(0);
		$storageKey = $this->getSessionSortStorageKey();
		if (!isset($_SESSION[$storageKey]) || !is_array($_SESSION[$storageKey])) {
			$_SESSION[$storageKey] = [];
		}
		$_SESSION[$storageKey][$pageId] = (string) $sortBy;
	}

	private function buildIndexQueryString($sort = '', $category = '', $author = '', $year = '')
	{
		$sort = (string) $sort;
		if (!array_key_exists($sort, self::$sortOptions)) {
			return '';
		}

		return '/sort/' . rawurlencode($sort);
	}

	private function getRequestedSortBy()
	{
		$requestedSort = '';

		if ($this->getUrl(1) === 'sort') {
			$requestedSort = trim((string) $this->getUrl(2));
		}
		elseif ($this->getUrl(2) === 'sort') {
			$requestedSort = trim((string) $this->getUrl(3));
		}
		elseif (isset($_GET['sort'])) {
			$requestedSort = trim((string) $_GET['sort']);
		}

		if ($requestedSort !== '' && array_key_exists($requestedSort, self::$sortOptions)) {
			return $requestedSort;
		}

		$storedSort = $this->getStoredVisitorSortBy();
		if ($storedSort !== '') {
			return $storedSort;
		}

		return $this->getConfiguredSortBy();
	}

	private function normalizeTagsInput($input)
	{
		if (is_array($input)) {
			$tags = $input;
		} else {
			$tags = preg_split('/[,;\r\n]+/u', (string) $input);
		}

		$normalized = [];
		foreach ((array) $tags as $tag) {
			$tag = html_entity_decode((string) $tag, ENT_QUOTES | ENT_HTML5, 'UTF-8');
			$tag = str_replace('#', ' ', $tag);
			$tag = trim(preg_replace('/\s+/u', ' ', $tag));
			if ($tag === '') {
				continue;
			}
			$normalized[mb_strtolower($tag, 'UTF-8')] = $tag;
			if (count($normalized) >= self::TAGS_LIMIT) {
				break;
			}
		}

		return array_values($normalized);
	}

	private function getArticleAuthorLabel(array $articleData)
	{
		$userId = (string) ($articleData['userId'] ?? '');
		if ($userId !== '' && $this->getData(['user', $userId]) !== null) {
			return trim(
				(string) $this->getData(['user', $userId, 'firstname']) . ' ' .
				(string) $this->getData(['user', $userId, 'lastname'])
			);
		}

		return '';
	}

	private function normalizeSearchText($text)
	{
		$text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$text = trim(strip_tags($text));
		$text = preg_replace('/\s+/u', ' ', $text);
		return mb_strtolower((string) $text, 'UTF-8');
	}

	private function getSearchTokens($search)
	{
		$tokens = preg_split('/[\s,;:!?()\[\]{}"“”«»\/\\|]+/u', mb_strtolower((string) $search, 'UTF-8'));
		$result = [];
		foreach ((array) $tokens as $token) {
			$token = trim($token);
			if ($token === '') {
				continue;
			}
			$result[$token] = $token;
		}
		return array_values($result);
	}

	private function countSearchOccurrences($needle, $haystack)
	{
		if ($needle === '' || $haystack === '') {
			return 0;
		}
		return preg_match_all('/' . preg_quote($needle, '/') . '/u', $haystack);
	}

	private function articlePassesSearchFilters(array $articleData, array $filters)
	{
		$category = $this->normalizeCategoryLabel($articleData['category'] ?? '');
		if ($filters['category'] !== '' && $category !== $filters['category']) {
			return false;
		}

		$userId = (string) ($articleData['userId'] ?? '');
		if ($filters['author'] !== '' && $userId !== $filters['author']) {
			return false;
		}

		if ($filters['year'] !== '') {
			$publishedOn = (int) ($articleData['publishedOn'] ?? 0);
			if ($publishedOn <= 0 || date('Y', $publishedOn) !== $filters['year']) {
				return false;
			}
		}

		return true;
	}

	private function getSearchMatchData(array $articleData, $search)
	{
		$search = trim((string) $search);
		$tokens = $this->getSearchTokens($search);
		$searchable = [
			'title' => $this->normalizeSearchText($articleData['title'] ?? ''),
			'category' => $this->normalizeSearchText($articleData['category'] ?? ''),
			'content' => $this->normalizeSearchText($articleData['content'] ?? ''),
			'author' => $this->normalizeSearchText($this->getArticleAuthorLabel($articleData)),
		];

		if ($search === '') {
			return [
				'matched' => true,
				'score' => 0,
				'tokens' => [],
				'excerpt' => ''
			];
		}

		$score = 0;
		$matchedTokens = [];
		foreach ($tokens as $token) {
			$titleHits = $this->countSearchOccurrences($token, $searchable['title']);
			$categoryHits = $this->countSearchOccurrences($token, $searchable['category']);
			$contentHits = $this->countSearchOccurrences($token, $searchable['content']);
			$authorHits = $this->countSearchOccurrences($token, $searchable['author']);
			$totalHits = $titleHits + $categoryHits + $contentHits + $authorHits;

			if ($totalHits === 0) {
				return [
					'matched' => false,
					'score' => 0,
					'tokens' => $tokens,
					'excerpt' => ''
				];
			}

			$matchedTokens[] = $token;
			$score += ($titleHits * 5) + ($categoryHits * 3) + ($contentHits * 1) + ($authorHits * 2);
		}

		return [
			'matched' => true,
			'score' => $score,
			'tokens' => $matchedTokens,
			'excerpt' => $this->buildSearchExcerpt($articleData['content'] ?? '', $matchedTokens)
		];
	}

	private function buildSearchExcerpt($content, array $tokens, $length = 240)
	{
		$text = html_entity_decode((string) $content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)));
		if ($text === '') {
			return '';
		}

		$start = 0;
		$firstPosition = null;
		foreach ($tokens as $token) {
			$position = mb_stripos($text, $token, 0, 'UTF-8');
			if ($position !== false && ($firstPosition === null || $position < $firstPosition)) {
				$firstPosition = $position;
			}
		}

		if ($firstPosition !== null) {
			$start = max(0, $firstPosition - 70);
		}

		$excerpt = mb_substr($text, $start, $length, 'UTF-8');
		if ($start > 0) {
			$excerpt = '…' . $excerpt;
		}
		if (($start + $length) < mb_strlen($text, 'UTF-8')) {
			$excerpt .= '…';
		}

		return $excerpt;
	}

	private function highlightSearchTerms($text, array $tokens)
	{
		$highlighted = $this->escapeDisplayText($text);
		if (empty($tokens)) {
			return $highlighted;
		}

		usort($tokens, function ($left, $right) {
			return mb_strlen($right, 'UTF-8') <=> mb_strlen($left, 'UTF-8');
		});

		foreach ($tokens as $token) {
			$escapedToken = htmlspecialchars((string) $token, ENT_QUOTES, 'UTF-8');
			if ($escapedToken === '') {
				continue;
			}
			$highlighted = preg_replace(
				'/' . preg_quote($escapedToken, '/') . '/iu',
				'<mark class="blogSearchMark">$0</mark>',
				$highlighted
			);
		}

		return $highlighted;
	}

	private function compareArticlesByText($left, $right, $field, $direction = 'asc')
	{
		$leftValue = $this->decodeDisplayText((string) ($left[$field] ?? ''));
		$rightValue = $this->decodeDisplayText((string) ($right[$field] ?? ''));

		if ($leftValue === $rightValue) {
			return strcasecmp($this->decodeDisplayText((string) ($left['title'] ?? '')), $this->decodeDisplayText((string) ($right['title'] ?? '')));
		}

		$comparison = strcasecmp($leftValue, $rightValue);
		return $direction === 'desc' ? -$comparison : $comparison;
	}

	private function compareArticlesBySort(array $a, array $b, $sortBy)
	{
		switch ($sortBy) {
			case 'title_asc':
				return $this->compareArticlesByText($a, $b, 'title', 'asc');
			case 'title_desc':
				return $this->compareArticlesByText($a, $b, 'title', 'desc');
			case 'category_asc':
				return $this->compareArticlesByText($a, $b, 'category', 'asc');
			case 'category_desc':
				return $this->compareArticlesByText($a, $b, 'category', 'desc');
			case 'publishedOn_asc':
				return (int) ($a['publishedOn'] ?? 0) <=> (int) ($b['publishedOn'] ?? 0);
			case 'publishedOn_desc':
			default:
				return (int) ($b['publishedOn'] ?? 0) <=> (int) ($a['publishedOn'] ?? 0);
		}
	}

	private function sortPublishedArticles(array &$articles, $sortBy, $preferScore = false)
	{
		uasort($articles, function ($a, $b) use ($sortBy, $preferScore) {
			if ($preferScore) {
				$scoreComparison = (int) ($b['_searchScore'] ?? 0) <=> (int) ($a['_searchScore'] ?? 0);
				if ($scoreComparison !== 0) {
					return $scoreComparison;
				}
			}
			return $this->compareArticlesBySort($a, $b, $sortBy);
		});
	}

	private function buildSearchFacets(array $articles)
	{
		$facets = [
			'categories' => [],
			'authors' => [],
			'years' => [],
		];

		foreach ($this->getConfiguredCategories() as $configuredCategory) {
			$configuredCategory = $this->normalizeCategoryLabel($configuredCategory);
			if ($configuredCategory !== '') {
				$facets['categories'][$configuredCategory] = $configuredCategory;
			}
		}

		foreach ($articles as $article) {
			$category = $this->normalizeCategoryLabel($article['category'] ?? '');
			if ($category !== '') {
				$facets['categories'][$category] = $category;
			}

			$userId = (string) ($article['userId'] ?? '');
			$authorLabel = $this->getArticleAuthorLabel($article);
			if ($userId !== '' && $authorLabel !== '') {
				$facets['authors'][$userId] = $authorLabel;
			}

			$publishedOn = (int) ($article['publishedOn'] ?? 0);
			if ($publishedOn > 0) {
				$year = date('Y', $publishedOn);
				$facets['years'][$year] = $year;
			}
		}

		natcasesort($facets['categories']);
		natcasesort($facets['authors']);
		krsort($facets['years'], SORT_NUMERIC);

		return $facets;
	}

	private function buildSearchSuggestions(array $articles, $search)
	{
		$search = trim((string) $search);
		if ($search === '') {
			return [];
		}

		$normalizedSearch = mb_strtolower($search, 'UTF-8');
		$candidates = [];

		foreach ($articles as $article) {
			$phrases = [
				$this->decodeDisplayText((string) ($article['title'] ?? '')),
				$this->decodeDisplayText((string) ($article['category'] ?? '')),
				$this->decodeDisplayText((string) $this->getArticleAuthorLabel($article)),
			];

			foreach ($phrases as $phrase) {
				$phrase = trim($phrase);
				if ($phrase === '') {
					continue;
				}

				$normalizedPhrase = mb_strtolower($phrase, 'UTF-8');
				if ($normalizedPhrase === $normalizedSearch || isset($candidates[$normalizedPhrase])) {
					continue;
				}

				if (mb_stripos($normalizedPhrase, $normalizedSearch, 0, 'UTF-8') !== false) {
					$candidates[$normalizedPhrase] = [
						'label' => $phrase,
						'score' => 100 - mb_strlen($phrase, 'UTF-8'),
					];
				}
			}
		}

		uasort($candidates, function ($left, $right) {
			return $right['score'] <=> $left['score'];
		});

		return array_slice(array_map(function ($item) {
			return $item['label'];
		}, $candidates), 0, 6);
	}

	private function prepareSearchPresentation(array $articleData, array $matchData, $search)
	{
		$tokens = (array) ($matchData['tokens'] ?? []);
		$articleData['_displayTitle'] = $this->highlightSearchTerms($articleData['title'] ?? '', $tokens);
		$articleData['_displayCategory'] = $this->highlightSearchTerms($articleData['category'] ?? '', $tokens);
		$articleData['_displayAuthor'] = $this->escapeDisplayText($this->getArticleAuthorLabel($articleData));
		$articleData['_searchScore'] = (int) ($matchData['score'] ?? 0);
		$articleData['_searchExcerpt'] = '';

		if ($search !== '') {
			$articleData['_searchExcerpt'] = $this->highlightSearchTerms((string) ($matchData['excerpt'] ?? ''), $tokens);
		}

		return $articleData;
	}

	/**
	 * Flux RSS
	 */
	public function rss()
	{
		// Inclure les classes
		include_once 'module/blog/vendor/FeedWriter/Item.php';
		include_once 'module/blog/vendor/FeedWriter/Feed.php';
		include_once 'module/blog/vendor/FeedWriter/RSS2.php';
		include_once 'module/blog/vendor/FeedWriter/InvalidOperationException.php';

		date_default_timezone_set('UTC');
		$feeds = new \FeedWriter\RSS2();

		// En-tête avec nettoyage du contenu
		$pageTitle = $this->getData(['page', $this->getUrl(0), 'title']);
		$feeds->setTitle($pageTitle ? helper::cleanRssText($pageTitle) : '');
		$feeds->setLink(helper::baseUrl() . $this->getUrl(0));
		if ($metaDescription = $this->getData(['page', $this->getUrl(0), 'metaDescription'])) {
			// Channel description should be plain text for best interoperability
			$feeds->setDescription(trim(strip_tags(helper::cleanRssText($metaDescription))));
		} else {
			// Fallback: use page title or base URL so the channel has a description (plain text)
			$fallbackDesc = $pageTitle ? $pageTitle : helper::baseUrl(false);
			$feeds->setDescription(trim(strip_tags(helper::cleanRssText($fallbackDesc))));
		}
		// Add content namespace for full HTML content (use FeedWriter API)
		if (method_exists($feeds, 'addNamespace')) {
			$feeds->addNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
		} else {
			$feeds->setChannelElement('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
		}
		// Determine atom:self based on current request URI when possible
		$scheme = helper::isHttps() ? 'https://' : 'http://';
		if (!empty($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
			// Use the actual request URI so atom:link matches the document location
			$selfHref = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} else {
			// Fallback to the canonical feed URL
			$selfHref = helper::baseUrl(false) . $this->getUrl(0) . '/rss';
		}
		// Use FeedWriter API to add atom:self properly
		if (method_exists($feeds, 'setSelfLink')) {
			$feeds->setSelfLink($selfHref);
		} else {
			$feeds->setAtomLink($selfHref, 'self', $feeds->getMIMEType());
		}
		$feeds->setChannelElement('language', 'fr-FR');
		$feeds->setDate(date('r', time()));
		$feeds->addGenerator();
		// Corps des articles
		$articleIdsPublishedOns = helper::arrayColumn($this->getData(['module', $this->getUrl(0), 'posts']), 'publishedOn', 'SORT_DESC');
		$articleIdsStates = helper::arrayColumn($this->getData(['module', $this->getUrl(0), 'posts']), 'state', 'SORT_DESC');
		foreach ($articleIdsPublishedOns as $articleId => $articlePublishedOn) {
			if ($articlePublishedOn <= time() and $articleIdsStates[$articleId]) {
				// Récupération des données de l'article
				$articleData = $this->getData(['module', $this->getUrl(0), 'posts', $articleId]);
				$articleTitle = helper::cleanRssText($articleData['title']);
				
				// Miniature
				$thumb = helper::baseUrl(false) . $this->getThumb($articleData['picture']);
				
				// Créer les articles du flux
				$newsArticle = $feeds->createNewItem();
				
				// Signature de l'article
				$author = $this->signature(helper::cleanRssText($articleData['userId'], true));
				
				// Construction du contenu avec nettoyage des données
				// description: plain text (no HTML) for validators; content:encoded holds full HTML
				$contentText = trim(strip_tags(helper::cleanRssText($articleData['content'])));
				// Limiter la description à 300 caractères pour un résumé clair
				if (strlen($contentText) > 300) {
					$plainDesc = substr($contentText, 0, 297) . '...';
				} else {
					$plainDesc = $contentText;
				}
				$fullHtml = '<img src="' . $thumb
					. '" alt="' . helper::cleanRssText($articleTitle, true)
					. '" title="' . helper::cleanRssText($articleTitle, true)
					. '" />' . helper::cleanRssText($articleData['content']);
				$newsArticle->addElementArray([
					'title' => $articleTitle,
					'link' => helper::baseUrl() . $this->getUrl(0) . '/' . $articleId,
					'description' => $plainDesc,
					'content:encoded' => $fullHtml
				]);
				$newsArticle->setAuthor($author, 'no@mail.com');
				$newsArticle->setId(helper::baseUrl() . $this->getUrl(0) . '/' . $articleId);
				$newsArticle->setDate(date('r', $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'publishedOn'])));
				if ($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'picture'])
					&& file_exists($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'picture']))) {
					$thumbPath = $this->getThumb($articleData['picture']);
					$imageData = getimagesize($thumbPath);
					$newsArticle->addEnclosure(
						helper::baseUrl(false) . $thumbPath,
						$imageData[0] * $imageData[1],
						$imageData['mime']
					);
				}
				$feeds->addItem($newsArticle);
			}
		}

		// Valeurs en sortie
		$this->addOutput([
			'display' => self::DISPLAY_RSS,
			'content' => $feeds->generateFeed(),
			'view' => 'rss'
		]);
	}

	/**
	 * Édition
	 */
	public function add()
	{
		// Soumission du formulaire
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) === true &&
			$this->isPost()
		) {
			// Modification de l'userId
			if ($this->getUser('role') === self::ROLE_ADMIN) {
				$newuserid = $this->getInput('blogAddUserId', helper::FILTER_STRING_SHORT, true);
			} else {
				$newuserid = $this->getUser('id');
			}
			// Incrémente l'id de l'article
			$articleId = helper::increment($this->getInput('blogAddPermalink'), $this->getData(['page']));
			$articleId = helper::increment($articleId, (array) $this->getData(['module', $this->getUrl(0)]));
			$articleId = helper::increment($articleId, array_keys(self::$actions));
			// Crée l'article
			$this->setData([
				'module',
				$this->getUrl(0),
				'posts',
				$articleId,
				[
					'content' => $this->getRelativePath($this->getInput('blogAddContent', null)),
					'picture' => $this->getNormalizedFilePath($this->getInput('blogAddPicture', helper::FILTER_STRING_SHORT),$this->getUser('id')),
					'hidePicture' => $this->getInput('blogAddHidePicture', helper::FILTER_BOOLEAN),
					'pictureSize' => $this->getInput('blogAddPictureSize', helper::FILTER_STRING_SHORT),
					'picturePosition' => $this->getInput('blogAddPicturePosition', helper::FILTER_STRING_SHORT),
					'publishedOn' => $this->getInput('blogAddPublishedOn', helper::FILTER_DATETIME, true),
					'state' => $this->getInput('blogAddState', helper::FILTER_BOOLEAN),
					'title' => $this->getInput('blogAddTitle', helper::FILTER_STRING_SHORT, true),
					'category' => $this->normalizeCategoryLabel($this->getInput('blogAddCategory', helper::FILTER_STRING_SHORT)),
					'tags' => $this->normalizeTagsInput($this->getInput('blogAddTags', null)),
					'userId' => $newuserid,
					'editConsent' => $this->getInput('blogAddConsent') === self::EDIT_ROLE ? $this->getUser('role') : $this->getInput('blogAddConsent'),
					'commentMaxlength' => $this->getInput('blogAddCommentMaxlength'),
					'commentApproved' => $this->getInput('blogAddCommentApproved', helper::FILTER_BOOLEAN),
					'commentClose' => $this->getInput('blogAddCommentClose', helper::FILTER_BOOLEAN),
					'commentNotification' => $this->getInput('blogAddCommentNotification', helper::FILTER_BOOLEAN),
					'commentGroupNotification' => $this->getInput('blogAddCommentGroupNotification', helper::FILTER_INT),
					'comment' => []
				]
			]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/config',
				'notification' => helper::translate('Nouvel article créé'),
				'state' => true
			]);
		}
		// Liste des utilisateurs
		self::$users = helper::arrayColumn($this->getData(['user']), 'firstname');
		ksort(self::$users);
		foreach (self::$users as $userId => &$userFirstname) {
			$userFirstname = $userFirstname . ' ' . $this->getData(['user', $userId, 'lastname']);
		}
		unset($userFirstname);
		// Valeurs en sortie
		$this->addOutput([
			'title' => helper::translate('Rédiger un article'),
			'vendor' => [
				'tinymce',
				'furl'
			],
			'view' => 'add'
		]);
	}

	/**
	 * Liste des commentaires
	 */
	public function comment()
	{
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		} else {
			$comments = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment']);
			self::$commentsDelete = template::button('blogCommentDeleteAll', [
				'class' => 'blogCommentDeleteAll buttonRed',
				'href' => helper::baseUrl() . $this->getUrl(0) . '/commentDeleteAll/' . $this->getUrl(2),
				'value' => 'Tout effacer'
			]);
			// Ids des commentaires par ordre de création
			$commentIds = array_keys(helper::arrayColumn($comments, 'createdOn', 'SORT_DESC'));
			// Pagination
			$pagination = helper::pagination($commentIds, $this->getUrl(), $this->getData(['module', $this->getUrl(0), 'config', 'itemsperPage']));
			// Liste des pages
			self::$pages = $pagination['pages'];
			// Commentaires en fonction de la pagination
			for ($i = $pagination['first']; $i < $pagination['last']; $i++) {
				// Met en forme le tableau
				$comment = $comments[$commentIds[$i]];
				// Bouton d'approbation
				$buttonApproval = '';
				// Compatibilité avec les commentaires des versions précédentes, les valider
				$comment['approval'] = array_key_exists('approval', $comment) === false ? true : $comment['approval'];
				if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentApproved']) === true) {
					$buttonApproval = template::button('blogCommentApproved' . $commentIds[$i], [
						'class' => $comment['approval'] === true ? 'blogCommentRejected buttonGreen' : 'blogCommentApproved buttonRed',
						'href' => helper::baseUrl() . $this->getUrl(0) . '/commentApprove/' . $this->getUrl(2) . '/' . $commentIds[$i],
						'value' => $comment['approval'] === true ? 'A' : 'R',
						'help' => $comment['approval'] === true ? 'Approuvé' : 'Rejeté',
					]);
				}
				self::$dateFormat = $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']);
				self::$timeFormat = $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']);
				self::$comments[] = [
					helper::dateUTF8(self::$dateFormat, $comment['createdOn'], self::$i18nUI) . ' - ' . helper::dateUTF8(self::$timeFormat, $comment['createdOn'], self::$i18nUI),
					$comment['content'],
					$comment['userId'] ? $this->getData(['user', $comment['userId'], 'firstname']) . ' ' . $this->getData(['user', $comment['userId'], 'lastname']) : $comment['author'],
					$buttonApproval,
					template::button('blogCommentDelete' . $commentIds[$i], [
						'class' => 'blogCommentDelete buttonRed',
						'href' => helper::baseUrl() . $this->getUrl(0) . '/commentDelete/' . $this->getUrl(2) . '/' . $commentIds[$i],
						'value' => template::ico('trash')
					])
				];
			}
			// Valeurs en sortie
			$this->addOutput([
				'title' => helper::translate('Gestion des commentaires'),
				'view' => 'comment'
			]);
		}
	}

	/**
	 * Suppression de commentaire
	 */
	public function commentDelete()
	{
		// Le commentaire n'existe pas
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true ||
			$this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3)]) === null
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// Suppression
		else {
			$this->deleteData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3)]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/comment/' . $this->getUrl(2),
				'notification' => helper::translate('Commentaire supprimé'),
				'state' => true
			]);
		}
	}

	/**
	 * Suppression de tous les commentairess de l'article $this->getUrl(2)
	 */
	public function commentDeleteAll()
	{
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// Suppression
		else {
			$this->setData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', []]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/comment',
				'notification' => helper::translate('Commentaires supprimés'),
				'state' => true
			]);
		}
	}

	/**
	 * Approbation oou désapprobation de commentaire
	 */
	public function commentApprove()
	{
		// Le commentaire n'existe pas
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true ||
			$this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3)]) === null
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// Inversion du statut
		else {
			$approved = !$this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3), 'approval']);
			$this->setData([
				'module',
				$this->getUrl(0),
				'posts',
				$this->getUrl(2),
				'comment',
				$this->getUrl(3),
				[
					'author' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3), 'author']),
					'content' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3), 'content']),
					'createdOn' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3), 'createdOn']),
					'userId' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3), 'userId']),
					'approval' => $approved
				]
			]);

			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/comment/' . $this->getUrl(2),
				'notification' => $approved ? helper::translate('Commentaire approuvé') : helper::translate('Commentaire rejeté'),
				'state' => $approved
			]);
		}
	}

	/**
	 * Configuration
	 */
	public function config()
	{

		// Mise à jour des données de module
		$this->update();

		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) === true &&
			$this->isPost()
		) {
			$this->setData(['module', $this->getUrl(0), 'config', 'sortBy', $this->getInput('blogConfigSortBy', helper::FILTER_STRING_SHORT)]);
			$this->setData(['module', $this->getUrl(0), 'config', 'categories', $this->normalizeCategoriesInput($this->getInput('blogConfigCategories', null))]);
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/config',
				'notification' => 'Configuration enregistrée',
				'state' => true
			]);
			return;
		}
		// Ids des articles par ordre de publication
		$articleIds = array_keys(helper::arrayColumn($this->getData(['module', $this->getUrl(0), 'posts']), 'publishedOn', 'SORT_DESC'));
		// Gestion des droits d'accès
		$filterData = [];
		foreach ($articleIds as $key => $value) {
			if (
				( // Propriétaire
					$this->getData(['module', $this->getUrl(0), 'posts', $value, 'editConsent']) === self::EDIT_OWNER
					and ($this->getData(['module', $this->getUrl(0), 'posts', $value, 'userId']) === $this->getUser('id')
						or $this->getUser('role') === self::ROLE_ADMIN)
				)

				or (
					// Rôle
					$this->getData(['module', $this->getUrl(0), 'posts', $value, 'editConsent']) !== self::EDIT_OWNER
					and $this->getUser('role') >= $this->getData(['module', $this->getUrl(0), 'posts', $value, 'editConsent'])
				)
				or (
					// Tout le monde
					$this->getData(['module', $this->getUrl(0), 'posts', $value, 'editConsent']) === self::EDIT_ALL
				)
			) {
				$filterData[] = $value;
			}
		}
		$articleIds = $filterData;
		// Pagination
		$pagination = helper::pagination($articleIds, $this->getUrl(), self::$itemsperPage);
		// Liste des pages
		self::$pages = $pagination['pages'];
		// Format de temps
		self::$dateFormat = $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']);
		self::$timeFormat = $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']);
		// Articles en fonction de la pagination
		for ($i = $pagination['first']; $i < $pagination['last']; $i++) {
			// Nombre de commentaires à approuver et approuvés
			$approvals = helper::arrayColumn($this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'comment']), 'approval', 'SORT_DESC');
			if (is_array($approvals)) {
				$a = array_values($approvals);
				$toApprove = count(array_keys($a, false));
				$approved = count(array_keys($a, true));
			} else {
				$toApprove = 0;
				$approved = count($this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'comment']));
			}
			// Met en forme le tableau
			self::$articles[] = [
				'<a href="' . helper::baseurl() . $this->getUrl(0) . '/' . $articleIds[$i] . '" target="_blank" >' .
				$this->escapeDisplayText($this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'title'])) .
				'</a>',
				helper::dateUTF8(self::$dateFormat, $this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'publishedOn']), self::$i18nUI) . ' - ' . helper::dateUTF8(self::$timeFormat, $this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'publishedOn']), self::$i18nUI),
				self::$states[$this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'state'])],
				// Bouton pour afficher les commentaires de l'article
				template::button('blogConfigComment' . $articleIds[$i], [
					'class' => ($toApprove || $approved) > 0 ? '' : 'buttonGrey',
					'href' => ($toApprove || $approved) > 0 ? helper::baseUrl() . $this->getUrl(0) . '/comment/' . $articleIds[$i] : '',
					'value' => $toApprove > 0 ? $toApprove . '/' . $approved : $approved,
					'help' => ($toApprove || $approved) > 0 ? 'Éditer  / Approuver un commentaire' : ''
				]),
				template::button('blogConfigEdit' . $articleIds[$i], [
					'href' => helper::baseUrl() . $this->getUrl(0) . '/edit/' . $articleIds[$i],
					'value' => template::ico('pencil')
				]),
				template::button('blogConfigDelete' . $articleIds[$i], [
					'class' => 'blogConfigDelete buttonRed',
					'href' => helper::baseUrl() . $this->getUrl(0) . '/delete/' . $articleIds[$i],
					'value' => template::ico('trash')
				])
			];
		}
		// Valeurs en sortie
		$this->addOutput([
			'title' => helper::translate('Configuration du module'),
			'view' => 'config'
		]);
	}

	public function option()
	{

		// Soumission du formulaire
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) === true &&
			$this->isPost()
		) {
			$this->setData([
				'module',
				$this->getUrl(0),
				'config',
				[
					'feeds' => $this->getInput('blogOptionShowFeeds', helper::FILTER_BOOLEAN),
					'feedsLabel' => $this->getInput('blogOptionFeedslabel', helper::FILTER_STRING_SHORT),
					'layout' => $this->getInput('blogOptionArticlesLayout', helper::FILTER_BOOLEAN),
					'articlesLenght' => $this->getInput('blogOptionArticlesLayout', helper::FILTER_BOOLEAN) === false ? $this->getInput('blogOptionArticlesLenght', helper::FILTER_INT) : 0,
					'itemsperPage' => $this->getInput('blogOptionItemsperPage', helper::FILTER_INT, true),
					'dateFormat' => $this->getInput('blogOptionDateFormat'),
					'timeFormat' => $this->getInput('blogOptionTimeFormat'),
					'buttonBack' => $this->getInput('blogOptionButtonBack', helper::FILTER_BOOLEAN),
					'showDate' => $this->getInput('blogOptionShowDate', helper::FILTER_BOOLEAN),
					'showTime' => $this->getInput('blogOptionShowTime', helper::FILTER_BOOLEAN),
					'showPseudo' => $this->getInput('blogOptionShowPseudo', helper::FILTER_BOOLEAN),
					'sortBy' => $this->getConfiguredSortBy(),
					'categories' => $this->getConfiguredCategories(),
					'versionData' => $this->getData(['module', $this->getUrl(0), 'config', 'versionData']),
				]
			]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/config',
				'notification' => helper::translate('Modifications enregistrées'),
				'state' => true
			]);
		}
		// Valeurs en sortie
		$this->addOutput([
			'title' => helper::translate('Options de configuration'),
			'view' => 'option'
		]);
	}

	/**
	 * Suppression
	 */
	public function delete()
	{
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true ||
			$this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2)]) === null
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// Suppression
		else {
			$this->deleteData(['module', $this->getUrl(0), 'posts', $this->getUrl(2)]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/config',
				'notification' => helper::translate('Article supprimé'),
				'state' => true
			]);
		}
	}

	/**
	 * Édition
	 */
	public function edit()
	{
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// L'article n'existe pas
		if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2)]) === null) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// L'article existe
		else {
			// Soumission du formulaire
			if (
				$this->getUser('permission', __CLASS__, __FUNCTION__) === true &&
				$this->isPost()
			) {
				if ($this->getUser('role') === self::ROLE_ADMIN) {
					$newuserid = $this->getInput('blogEditUserId', helper::FILTER_STRING_SHORT, true);
				} else {
					$newuserid = $this->getUser('id');
				}
				$articleId = $this->getInput('blogEditPermalink', null, true);
				// Incrémente le nouvel id de l'article
				if ($articleId !== $this->getUrl(2)) {
					$articleId = helper::increment($articleId, $this->getData(['page']));
					$articleId = helper::increment($articleId, $this->getData(['module', $this->getUrl(0), 'posts']));
					$articleId = helper::increment($articleId, array_keys(self::$actions));
				}
				$this->setData([
					'module',
					$this->getUrl(0),
					'posts',
					$articleId,
					[
						'title' => $this->getInput('blogEditTitle', helper::FILTER_STRING_SHORT, true),
						'category' => $this->normalizeCategoryLabel($this->getInput('blogEditCategory', helper::FILTER_STRING_SHORT)),
						'tags' => $this->normalizeTagsInput($this->getInput('blogEditTags', null)),
						'comment' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment']),
						'content' => $this->getRelativePath($this->getInput('blogEditContent', null)),
						'picture' => $this->getNormalizedFilePath($this->getInput('blogEditPicture', helper::FILTER_STRING_SHORT), $this->getUser('id')),
						'hidePicture' => $this->getInput('blogEditHidePicture', helper::FILTER_BOOLEAN),
						'pictureSize' => $this->getInput('blogEditPictureSize', helper::FILTER_STRING_SHORT),
						'picturePosition' => $this->getInput('blogEditPicturePosition', helper::FILTER_STRING_SHORT),
						'publishedOn' => $this->getInput('blogEditPublishedOn', helper::FILTER_DATETIME, true),
						'state' => $this->getInput('blogEditState', helper::FILTER_BOOLEAN),
						'userId' => $newuserid,
						'editConsent' => $this->getInput('blogEditConsent') === self::EDIT_ROLE ? $this->getUser('role') : $this->getInput('blogEditConsent'),
						'commentMaxlength' => $this->getInput('blogEditCommentMaxlength'),
						'commentApproved' => $this->getInput('blogEditCommentApproved', helper::FILTER_BOOLEAN),
						'commentClose' => $this->getInput('blogEditCommentClose', helper::FILTER_BOOLEAN),
						'commentNotification' => $this->getInput('blogEditCommentNotification', helper::FILTER_BOOLEAN),
						'commentGroupNotification' => $this->getInput('blogEditCommentGroupNotification', helper::FILTER_INT)
					]
				]);
				// Supprime l'ancien article
				if ($articleId !== $this->getUrl(2)) {
					$this->deleteData(['module', $this->getUrl(0), 'posts', $this->getUrl(2)]);
				}
				// Valeurs en sortie
				$this->addOutput([
					'redirect' => helper::baseUrl() . $this->getUrl(0) . '/config',
					'notification' => helper::translate('Modifications enregistrées'),
					'state' => true
				]);
			}
			// Liste des utilisateurs
			self::$users = helper::arrayColumn($this->getData(['user']), 'firstname');
			ksort(self::$users);
			foreach (self::$users as $userId => &$userFirstname) {
				// Les membres ne sont pas éditeurs, les exclure de la liste
				if ($this->getData(['user', $userId, 'role']) < self::ROLE_EDITOR) {
					unset(self::$users[$userId]);
				}
				$userFirstname = $userFirstname . ' ' . $this->getData(['user', $userId, 'lastname']) . ' (' . self::$roleEdits[$this->getData(['user', $userId, 'role'])] . ')';
			}
			unset($userFirstname);
			// Valeurs en sortie
			$this->addOutput([
				'title' => $this->decodeDisplayText($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'title'])),
				'vendor' => [
					'tinymce',
					'furl'
				],
				'view' => 'edit'
			]);
		}
	}


	/**
	 * Rend la liste publique des articles selon le tri demandé.
	 */
	private function renderPublicIndex($sortBy)
	{
		if (!array_key_exists((string) $sortBy, self::$sortOptions)) {
			$sortBy = $this->getConfiguredSortBy();
		}

		self::$currentSortBy = $sortBy;
		self::$currentCategory = '';
		self::$currentSearch = '';
		self::$indexQueryString = $this->buildIndexQueryString($sortBy);
		self::$articles = [];
		self::$comments = [];
		self::$categories = $this->getConfiguredCategories();

		$posts = $this->getData(['module', $this->getUrl(0), 'posts']);
		$publishedArticles = [];

		if (is_array($posts)) {
			foreach ($posts as $articleId => $articleData) {
				$publishedOn = (int) ($articleData['publishedOn'] ?? 0);
				$state = !empty($articleData['state']);
				if ($publishedOn > time() || $state !== true) {
					continue;
				}

				$articleData['category'] = $this->normalizeCategoryLabel($articleData['category'] ?? '');
				$articleData['tags'] = $this->normalizeTagsInput($articleData['tags'] ?? []);
				$publishedArticles[$articleId] = $articleData;

				self::$comments[$articleId] = 0;
				if (is_array($articleData['comment'] ?? null)) {
					foreach ($articleData['comment'] as $commentValue) {
						if (!empty($commentValue['approval'])) {
							self::$comments[$articleId]++;
						}
					}
				}
			}
		}

		$this->sortPublishedArticles($publishedArticles, $sortBy, false);
		$articleIds = array_keys($publishedArticles);
		$pagination = helper::pagination($articleIds, $this->getUrl(), $this->getData(['module', $this->getUrl(0), 'config', 'itemsperPage']), '#article');
		self::$pages = $pagination['pages'];

		for ($i = $pagination['first']; $i < $pagination['last']; $i++) {
			if (!isset($articleIds[$i])) {
				continue;
			}
			self::$articles[$articleIds[$i]] = $publishedArticles[$articleIds[$i]];
		}

		self::$dateFormat = $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']);
		self::$timeFormat = $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']);
		$this->addOutput([
			'showBarEditButton' => true,
			'showPageContent' => true,
			'view' => 'index'
		]);
	}

	/**
	 * Tri visiteur via route native : /<page>/sort/<cle>
	 */
	public function sort()
	{
		$this->update();
		$sortBy = $this->getRequestedSortBy();
		$this->setStoredVisitorSortBy($sortBy);
		$this->addOutput([
			'redirect' => helper::baseUrl() . $this->getUrl(0) . '#article',
			'notification' => '',
			'state' => true
		]);
	}

	/**
	 * Accueil (deux affichages en un pour éviter une url à rallonge)
	 */
	public function index()
	{
		// Mise à jour des données de module
		$this->update();
		// Affichage d'un article
		if (
			$this->getUrl(1)
			and intval($this->getUrl(1)) === 0
		) {
			if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1)]) === null) {
				$this->addOutput([
					'access' => false
				]);
			}
			else {
				if ($this->isPost()) {
					if (
						$this->isConnected() === false
						and password_verify($this->getInput('blogArticleCaptcha', helper::FILTER_INT), $this->getInput('blogArticleCaptchaResult')) === false
					) {
						self::$inputNotices['blogArticleCaptcha'] = 'Incorrect';
					} else {
						$commentId = helper::increment(uniqid(), $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment']));
						$content = $this->getInput('blogArticleContent', null, true);
						$this->setData([
							'module',
							$this->getUrl(0),
							'posts',
							$this->getUrl(1),
							'comment',
							$commentId,
							[
								'author' => $this->getInput('blogArticleAuthor', helper::FILTER_STRING_SHORT, empty($this->getInput('blogArticleUserId')) ? TRUE : FALSE),
								'content' => $this->getRelativePath($content),
								'createdOn' => time(),
								'userId' => $this->getInput('blogArticleUserId'),
								'approval' => !$this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'commentApproved'])
							]
						]);
						$to = [];
						foreach ($this->getData(['user']) as $userId => $user) {
							if ($user['role'] >= $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'commentGroupNotification'])) {
								$to[] = $user['mail'];
								$firstname[] = $user['firstname'];
								$lastname[] = $user['lastname'];
							}
						}
						$notification = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'commentApproved']) === true ? 'Commentaire déposé en attente d\'approbation' : 'Commentaire déposé';
						if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'commentNotification']) === true) {
							$error = 0;
							foreach ($to as $key => $adress) {
								$sent = $this->sendMail(
									$adress,
									'Nouveau commentaire déposé',
									'<p>Bonjour' . ' <strong>' . $firstname[$key] . ' ' . $lastname[$key] . '</strong>,</p>' .
									'<p>L\'article <a href="' . helper::baseUrl() . $this->getUrl(0) . '/	' . $this->getUrl(1) . '">' . $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'title']) . '</a> a  reçu un nouveau commentaire rédigé par <strong>' .
									$this->getInput('blogArticleAuthor', helper::FILTER_STRING_SHORT, empty($this->getInput('blogArticleUserId')) ? TRUE : FALSE) . '</strong></p>' .
									'<p>' . $content.'</p>',
									null,
									$this->getData(['config', 'smtp', 'from'])
								);
								if ($sent === false) {
									$error++;
								}
							}
							$this->addOutput([
								'redirect' => helper::baseUrl() . $this->getUrl() . '#comment',
								'notification' => ($error === 0 ? $notification . '<br/>Une notification a été envoyée.' : $notification . '<br/> Erreur de notification : ' . $sent),
								'state' => ($sent === true ? true : null)
							]);
						} else {
							$this->addOutput([
								'redirect' => helper::baseUrl() . $this->getUrl() . '#comment',
								'notification' => $notification,
								'state' => true
							]);
						}
					}
				}
				$commentsApproved = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment']);
				if ($commentsApproved) {
					foreach ($commentsApproved as $key => $value) {
						if ($value['approval'] === false) {
							unset($commentsApproved[$key]);
						}
					}
					self::$nbCommentsApproved = count($commentsApproved);
				}
				$commentIds = array_keys(helper::arrayColumn($commentsApproved, 'createdOn', 'SORT_DESC'));
				$pagination = helper::pagination($commentIds, $this->getUrl(), $this->getData(['module', $this->getUrl(0), 'config', 'itemsperPage']), '#comment');
				self::$pages = $pagination['pages'];
				if ($this->isConnected() === true) {
					self::$editCommentSignature = $this->signature($this->getUser('id'));
				}
				for ($i = $pagination['first']; $i < $pagination['last']; $i++) {
					$e = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment', $commentIds[$i], 'userId']);
					if ($e) {
						self::$commentsSignature[$commentIds[$i]] = $this->signature($e);
					} else {
						self::$commentsSignature[$commentIds[$i]] = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment', $commentIds[$i], 'author']);
					}
					if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment', $commentIds[$i], 'approval']) === true) {
						self::$comments[$commentIds[$i]] = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment', $commentIds[$i]]);
					}
				}
				self::$dateFormat = $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']);
				self::$timeFormat = $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']);
				self::$currentSortBy = $this->getRequestedSortBy();
				self::$currentCategory = '';
				self::$currentSearch = '';
				self::$indexQueryString = $this->buildIndexQueryString(self::$currentSortBy);
				$this->addOutput([
					'showBarEditButton' => true,
					'title' => $this->decodeDisplayText($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'title'])),
					'vendor' => [
						'tinymce'
					],
					'view' => 'article'
				]);
			}
		}
		else {
			$this->renderPublicIndex($this->getRequestedSortBy());
		}
	}


	/**
	 * Export PDF (gabarit A4) — génération client via html2pdf embarqué dans le module.
	 * URL : /<pageBlog>/pdf/<postId>
	 */
	public function pdf() {
		$pageId = $this->getUrl(0);
		$postId = $this->getUrl(2);

		if (empty($pageId) || empty($postId)) {
			http_response_code(404);
			exit;
		}

		$post = $this->getData(['module', $pageId, 'posts', $postId]);
		if ($post === null) {
			http_response_code(404);
			exit;
		}

		$title = html_entity_decode(strip_tags($post['title'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$publishedOn = $post['publishedOn'] ?? time();
		$dateTxt = date('d/m/Y', (int)$publishedOn);
		$timeTxt = date('H\hi', (int)$publishedOn);

		// Image "de l’article" (illustration d’en-tête)
		$hasPicture = !empty($post['picture']);
		$picture = $hasPicture ? $post['picture'] : '';

		// Contenu (HTML) — on décode les entités (&eacute; etc.)
		$contentHtml = html_entity_decode($post['content'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');

		$base = helper::baseUrl(false);

		header('Content-Type: text/html; charset=UTF-8');

		echo '<!doctype html><html lang="fr"><head>';
		echo '<meta charset="utf-8">';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
		echo '<base href="' . $base . '">';
		echo '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>';

		// Style A4 (propre, imprimable)
		echo '<style>
			:root{color-scheme:light;}
			body{margin:0;background:#f3f4f6;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,Arial,sans-serif;color:#111;}
			#pdfDoc{max-width:820px;margin:24px auto;background:#fff;padding:42px 54px;box-shadow:0 10px 30px rgba(0,0,0,.12);border-radius:10px;}
			h1{font-size:28px;line-height:1.2;margin:0 0 10px 0;letter-spacing:-.02em;}
			.meta{font-size:12px;color:#555;margin:0 0 22px 0;}
			hr{border:0;border-top:1px solid #e6e6e6;margin:22px 0;}
			img.blogPdfCover{width:100%;height:auto;border-radius:8px;margin:16px 0 18px 0;}
			article{font-size:15px;line-height:1.65;}
			article img{max-width:100%;height:auto;border-radius:6px;}
			article blockquote{margin:16px 0;padding:10px 14px;border-left:3px solid #d1d5db;background:#f9fafb;border-radius:6px;}
			article h2{font-size:20px;margin:20px 0 10px 0;}
			article h3{font-size:17px;margin:18px 0 8px 0;}
			.note{max-width:820px;margin:14px auto 0 auto;padding:10px 12px;color:#6b7280;font-size:12px;text-align:center;}
			@media print{
				body{background:#fff;}
				#pdfDoc{box-shadow:none;border-radius:0;margin:0;max-width:none;padding:18mm 16mm;}
				.note{display:none;}
			}
		</style>';

		// html2pdf local
		echo '<script src="module/blog/vendor/html2pdf/html2pdf.bundle.min.js"></script>';

		echo '</head><body>';

		echo '<div id="pdfDoc">';
		echo '<h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';
		echo '<p class="meta">' . $dateTxt . ' — ' . $timeTxt . '</p>';
		echo '<hr>';

		if (!empty($picture)) {
			echo '<img class="blogPdfCover" src="' . htmlspecialchars($picture, ENT_QUOTES, 'UTF-8') . '" alt="">';
		}

		echo '<article>' . $contentHtml . '</article>';
		echo '</div>';

		echo '<div class="note" id="pdfFallback" style="display:none">PDF : bibliothèque indisponible — impression navigateur.</div>';

		// Auto-export
		echo '<script>
			(function(){
				function fallback(){
					var n=document.getElementById("pdfFallback");
					if(n){n.style.display="block";}
					setTimeout(function(){ window.print(); }, 200);
				}
				if(!window.html2pdf){ fallback(); return; }
				var el=document.getElementById("pdfDoc");
				var filename = ('. json_encode($title) . ' || "article").toString()
					.replace(/["<>:\\/\\|\\?\\*]+/g,"")
					.replace(/\\s+/g," ")
					.trim() || "article";
				var opt = {
					margin: [12, 12, 14, 12],
					filename: filename + ".pdf",
					image: { type: "jpeg", quality: 0.98 },
					html2canvas: { scale: 2.5, useCORS: true, logging: false },
					jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
					pagebreak: { mode: ["css", "legacy"] }
				};
				html2pdf().set(opt).from(el).save().catch(fallback);
			})();
		</script>';

		echo '</body></html>';
		exit;
	}

	/**
	 * Export Markdown — URL : /<pageBlog>/md/<postId>
	 */
	public function md() {
		$pageId = $this->getUrl(0);
		$postId = $this->getUrl(2);

		if (empty($pageId) || empty($postId)) {
			http_response_code(404);
			exit;
		}

		$post = $this->getData(['module', $pageId, 'posts', $postId]);
		if ($post === null) {
			http_response_code(404);
			exit;
		}

		$title = html_entity_decode(strip_tags($post['title'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$publishedOn = $post['publishedOn'] ?? time();

		$pictureMd = $this->exportFeaturedImageMarkdown($post);

		$contentHtml = html_entity_decode($post['content'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$mdBody = $this->exportHtmlToMarkdown($contentHtml);

		$out = "# " . trim($title) . "\n\n";
		if ($pictureMd) {
			$out .= $pictureMd . "\n\n";
		}
		$out .= $mdBody . "\n";

		$filename = $this->exportSafeFilename($title ?: ('article-' . $postId)) . '.md';

		header('Content-Type: text/markdown; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		echo $out;
		exit;
	}

	/**
	 * Export EPUB — URL : /<pageBlog>/epub/<postId>
	 */
	public function epub() {
		$pageId = $this->getUrl(0);
		$postId = $this->getUrl(2);

		if (empty($pageId) || empty($postId)) {
			http_response_code(404);
			exit;
		}

		$post = $this->getData(['module', $pageId, 'posts', $postId]);
		if ($post === null) {
			http_response_code(404);
			exit;
		}

		if (!class_exists('ZipArchive')) {
			http_response_code(500);
			echo 'EPUB indisponible : extension ZipArchive manquante.';
			exit;
		}

		$title = html_entity_decode(strip_tags($post['title'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$publishedOn = $post['publishedOn'] ?? time();
		$dateIso = date('Y-m-d', (int)$publishedOn);

		// HTML (entités décodées)
		$contentHtml = html_entity_decode($post['content'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');

		// Collecte des images (dont l’image d’en-tête)
		$imagesMap = $this->exportCollectLocalImages($post, $contentHtml);

		// Remplacement des src pour l’EPUB
		foreach ($imagesMap as $src => $target) {
			$contentHtml = str_replace($src, $target, $contentHtml);
		}

		// XHTML simple
		$xhtml = '<!doctype html><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr"><head>'
			. '<meta charset="utf-8" />'
			. '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>'
			. '<link rel="stylesheet" type="text/css" href="styles.css" />'
			. '</head><body>'
			. '<h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';

		// Image d’en-tête en premier
		if (!empty($post['picture']) && isset($imagesMap[$post['picture']])) {
			$xhtml .= '<p><img alt="" src="' . htmlspecialchars($imagesMap[$post['picture']], ENT_QUOTES, 'UTF-8') . '" /></p>';
		}

		$xhtml .= '<div class="content">' . $contentHtml . '</div>'
			. '</body></html>';

		$css = "body{font-family:serif;line-height:1.5;}h1{font-size:1.6em;}img{max-width:100%;height:auto;}blockquote{border-left:3px solid #999;padding-left:10px;color:#444;}";

		$uid = 'urn:uuid:' . $this->exportUuidV4();
		$opf = '<?xml version="1.0" encoding="utf-8"?>'
			. '<package xmlns="http://www.idpf.org/2007/opf" version="3.0" unique-identifier="BookId">'
			. '<metadata xmlns:dc="http://purl.org/dc/elements/1.1/">'
			. '<dc:identifier id="BookId">' . htmlspecialchars($uid, ENT_QUOTES, 'UTF-8') . '</dc:identifier>'
			. '<dc:title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</dc:title>'
			. '<dc:language>fr</dc:language>'
			. '<dc:date>' . $dateIso . '</dc:date>'
			. '</metadata>'
			. '<manifest>'
			. '<item id="content" href="content.xhtml" media-type="application/xhtml+xml"/>'
			. '<item id="css" href="styles.css" media-type="text/css"/>';

		$spine = '<spine><itemref idref="content"/></spine>';

		$i = 1;
		foreach ($imagesMap as $src => $target) {
			$ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
			$mime = $this->exportMimeFromExt($ext);
			if (!$mime) continue;
			$opf .= '<item id="img' . $i . '" href="' . htmlspecialchars($target, ENT_QUOTES, 'UTF-8') . '" media-type="' . $mime . '"/>';
			$i++;
		}

		$opf .= '</manifest>' . $spine . '</package>';

		$containerXml = '<?xml version="1.0" encoding="utf-8"?>'
			. '<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">'
			. '<rootfiles><rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/></rootfiles>'
			. '</container>';

		// Création du fichier EPUB
		$tmpBase = tempnam(sys_get_temp_dir(), 'zwii_epub_');
		if ($tmpBase === false) {
			http_response_code(500);
			echo 'EPUB : impossible de créer un fichier temporaire.';
			exit;
		}
		@unlink($tmpBase);
		$epubPath = $tmpBase . '.epub';

		$zip = new ZipArchive();
		if ($zip->open($epubPath, ZipArchive::CREATE) !== true) {
			http_response_code(500);
			echo 'EPUB : impossible d’ouvrir l’archive.';
			exit;
		}

		// mimetype doit être le 1er, non compressé
		$zip->addFromString('mimetype', 'application/epub+zip');
		if (method_exists($zip, 'setCompressionName')) {
			$zip->setCompressionName('mimetype', ZipArchive::CM_STORE);
		}

		$zip->addFromString('META-INF/container.xml', $containerXml);
		$zip->addFromString('OEBPS/content.xhtml', $xhtml);
		$zip->addFromString('OEBPS/styles.css', $css);
		$zip->addFromString('OEBPS/content.opf', $opf);

		// Ajout des images locales
		$root = dirname(__FILE__, 3);
		foreach ($imagesMap as $src => $target) {
			$abs = $root . '/' . ltrim($src, '/');
			if (is_file($abs)) {
				$zip->addFile($abs, 'OEBPS/' . $target);
			}
		}

		$zip->close();

		$filename = $this->exportSafeFilename($title ?: ('article-' . $postId)) . '.epub';
		header('Content-Type: application/epub+zip');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . filesize($epubPath));
		readfile($epubPath);
		@unlink($epubPath);
		exit;
	}

	/* =============================
	   Helpers export
	   ============================= */

	private function exportSafeFilename($name) {
		$name = trim((string)$name);
		$name = preg_replace('/\s+/u', ' ', $name);
		$name = preg_replace('/[^\p{L}\p{N}\s\-_]+/u', '', $name);
		$name = trim($name);
		if ($name === '') $name = 'export';
		return substr($name, 0, 80);
	}

	private function exportFeaturedImageMarkdown($post) {
		if (empty($post['picture'])) return '';
		$src = (string)$post['picture'];
		$alt = basename(parse_url($src, PHP_URL_PATH) ?: $src);
		$abs = preg_match('#^https?://#i', $src) ? $src : (helper::baseUrl(false) . ltrim($src, '/'));
		return '![' . $alt . '](' . $abs . ')';
	}

	private function exportHtmlToMarkdown($html) {
		$html = (string)$html;

		// Images -> Markdown (avec absolutisation si local)
		$html = preg_replace_callback('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', function($m){
			$src = $m[1];
			$abs = preg_match('#^https?://#i', $src) ? $src : (helper::baseUrl(false) . ltrim($src, '/'));
			return "\n![](" . $abs . ")\n";
		}, $html);

		// Liens
		$html = preg_replace('/<a[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', '[$2]($1)', $html);

		// Titres
		$html = preg_replace('/<h1[^>]*>(.*?)<\/h1>/is', "\n# $1\n", $html);
		$html = preg_replace('/<h2[^>]*>(.*?)<\/h2>/is', "\n## $1\n", $html);
		$html = preg_replace('/<h3[^>]*>(.*?)<\/h3>/is', "\n### $1\n", $html);

		// Gras/italique
		$html = preg_replace('/<(strong|b)>(.*?)<\/\1>/is', '**$2**', $html);
		$html = preg_replace('/<(em|i)>(.*?)<\/\1>/is', '*$2*', $html);

		// Listes simples
		$html = preg_replace('/<li[^>]*>(.*?)<\/li>/is', "- $1\n", $html);
		$html = preg_replace('/<\/?(ul|ol)[^>]*>/i', "\n", $html);

		// Paragraphes / sauts
		$html = preg_replace('/<br\s*\/?>/i', "\n", $html);
		$html = preg_replace('/<\/p>/i', "\n\n", $html);
		$html = preg_replace('/<p[^>]*>/i', '', $html);

		// Blockquote
		$html = preg_replace('/<blockquote[^>]*>(.*?)<\/blockquote>/is', "\n> $1\n", $html);

		// Nettoyage tags restants
		$html = strip_tags($html);

		// Nettoyage entités résiduelles et espaces
		$html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$html = str_replace("\xc2\xa0", ' ', $html);
		$html = preg_replace("/[ 	]+\n/", "\n", $html);
		$html = preg_replace("/\n{3,}/", "\n\n", $html);
		return trim($html);
	}

	private function exportCollectLocalImages($post, $contentHtml) {
		$root = dirname(__FILE__, 3);
		$map = [];

		// Featured image
		if (!empty($post['picture'])) {
			$src = (string)$post['picture'];
			$abs = $root . '/' . ltrim($src, '/');
			if (is_file($abs)) {
				$target = 'images/cover' . '.' . strtolower(pathinfo($abs, PATHINFO_EXTENSION));
				$map[$src] = $target;
			}
		}

		// Inline images
		if (preg_match_all('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', (string)$contentHtml, $m)) {
			$srcs = array_values(array_unique($m[1]));
			$n = 1;
			foreach ($srcs as $src) {
				if (isset($map[$src])) continue;
				if (preg_match('#^https?://#i', $src) || str_starts_with($src, 'data:')) continue;
				$abs = $root . '/' . ltrim($src, '/');
				if (!is_file($abs)) continue;
				$ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
				if (!$this->exportMimeFromExt($ext)) continue;
				$map[$src] = 'images/img' . $n . '.' . $ext;
				$n++;
			}
		}

		return $map;
	}

	private function exportMimeFromExt($ext) {
		$ext = strtolower((string)$ext);
		$map = [
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png' => 'image/png',
			'gif' => 'image/gif',
			'webp' => 'image/webp',
			'svg' => 'image/svg+xml'
		];
		return $map[$ext] ?? null;
	}

	private function exportUuidV4() {
		$data = random_bytes(16);
		$data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
		$data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}


}