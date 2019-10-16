<?php

use Symfony\Component\Templating\PhpEngine;
use App\Helper\View\I18ns;
use App\Helper\View\TranslateHelper;

$searchQuery = $_GET['searchQuery'] ?? '';
$searchButtonType = $searchQuery ? 'remove' : 'search';

/** @var $view PhpEngine */
?>
<script type="text/javascript">
  addTranslate('ARTICLES_FOUND', '<?= TranslateHelper::text(I18ns::ARTICLES_FOUND) ?>');
</script>

<script src="/assets/js/search-form.js"></script>

<div class="search-form-block">
    <form method="get" action="/search" autocomplete="off" class="search">
        <div class="search-form">
            <div class="search-wrapper">
                <input type="text"
                    class="search-input"
                    name="searchQuery"
                    value="<?= $searchQuery ?>"
                    placeholder="<?= TranslateHelper::text(I18ns::SEARCH_PLACEHOLDER) ?>"
                    oninput="changeSearchValue(event)"
                    onclick="searchClickAction(event)"
                    autocomplete="off"
                >
                <input type="submit" style="display:none">

                <div class="search-button <?= $searchButtonType ?>"
                    data-type="<?= $searchButtonType ?>"
                    onclick="searchAction(event)"
                >
                    <div class="loading-animation-elements">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>

                <div class="search-autocomplete"></div>
            </div>
        </div>
    </form>
</div>
