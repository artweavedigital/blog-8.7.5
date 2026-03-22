<?php $moduleData['blog'] = [
    'add' => $this->getInput('profilAddBlogAdd', helper::FILTER_BOOLEAN),
    'edit' => $this->getInput('profilAddBlogEdit', helper::FILTER_BOOLEAN),
    'delete' => $this->getInput('profilAddBlogDelete', helper::FILTER_BOOLEAN),
    'option' => $this->getInput('profilAddBlogOption', helper::FILTER_BOOLEAN),
    'comment' => $this->getInput('profilAddBlogComment', helper::FILTER_BOOLEAN),
    'commentApprove' => $this->getInput('profilAddBlogCommentApprove', helper::FILTER_BOOLEAN),
    'commentDelete' => $this->getInput('profilAddBlogCommentDelete', helper::FILTER_BOOLEAN),
    'commentDeleteAll' => $this->getInput('profilAddBlogCommentDeleteAll', helper::FILTER_BOOLEAN),
    'config' => $this->getInput('profilAddBlogAdd', helper::FILTER_BOOLEAN) ||
    $this->getInput('profilAddBlogEdit', helper::FILTER_BOOLEAN) ||
    $this->getInput('profilAddBlogDelete', helper::FILTER_BOOLEAN) ||
    $this->getInput('profilAddBlogOption', helper::FILTER_BOOLEAN) ||
    $this->getInput('profilAddBlogComment', helper::FILTER_BOOLEAN) ||
    $this->getInput('profilAddBlogCommentApprove', helper::FILTER_BOOLEAN) ||
    $this->getInput('profilAddBlogCommentDelete', helper::FILTER_BOOLEAN) ||
    $this->getInput('profilAddBlogCommentDeleteAll', helper::FILTER_BOOLEAN)
];