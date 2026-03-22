<?php $moduleData['blog'] = [
	'add' => $this->getInput('profilEditBlogAdd', helper::FILTER_BOOLEAN),
	'edit' => $this->getInput('profilEditBlogEdit', helper::FILTER_BOOLEAN),
	'delete' => $this->getInput('profilEditBlogDelete', helper::FILTER_BOOLEAN),
	'option' => $this->getInput('profilEditBlogOption', helper::FILTER_BOOLEAN),
	'comment' => $this->getInput('profilEditBlogComment', helper::FILTER_BOOLEAN),
	'commentApprove' => $this->getInput('profilEditBlogCommentApprove', helper::FILTER_BOOLEAN),
	'commentDelete' => $this->getInput('profilEditBlogCommentDelete', helper::FILTER_BOOLEAN),
	'commentDeleteAll' => $this->getInput('profilEditBlogCommentDeleteAll', helper::FILTER_BOOLEAN),
	'config' => $this->getInput('profilEditBlogAdd', helper::FILTER_BOOLEAN) ||
	$this->getInput('profilEditBlogEdit', helper::FILTER_BOOLEAN) ||
	$this->getInput('profilEditBlogDelete', helper::FILTER_BOOLEAN) ||
	$this->getInput('profilEditBlogOption', helper::FILTER_BOOLEAN) ||
	$this->getInput('profilEditBlogComment', helper::FILTER_BOOLEAN) ||
	$this->getInput('profilEditBlogCommentApprove', helper::FILTER_BOOLEAN) ||
	$this->getInput('profilEditBlogCommentDelete', helper::FILTER_BOOLEAN) ||
	$this->getInput('profilEditBlogCommentDeleteAll', helper::FILTER_BOOLEAN)
];