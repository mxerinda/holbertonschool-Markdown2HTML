<?php
/** @var \Markdown2Html\Theme\DefaultTheme $theme */

use Markdown2Html\Theme\NavItem;

$currentItem = $theme->getCurrentNavItem();

$baseUrl = $currentItem ? $currentItem->getRelBaseUrl() : '';

if (!function_exists('buildNavItem')) {
    function buildNavItem(NavItem $navItem, $baseUrl, $currentItemId)
    {
        $isActive = false;
        if ($currentItemId === $navItem->id) {
            $isActive = true;
        }
        if ($navItem->getChildById($currentItemId)) {
            $isActive = true;
        }

        echo sprintf('<li class="%s">', $isActive ? 'active' : '');
        echo sprintf('<a href="%s">%s</a>', $baseUrl . $navItem->relUrl, $navItem->label);

        if ($navItem->children && $isActive) {
            echo '<ul>';
            foreach ($navItem->children as $child) {
                buildNavItem($child, $baseUrl, $currentItemId);
            }
            echo '</ul>';
        }

        echo '</li>';
    }
}

?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title><?php echo $theme->title ?></title>

    <link rel="stylesheet" href="<?php echo $baseUrl . 'assets/css/normalize.css' ?>">
    <link rel="stylesheet" href="<?php echo $baseUrl . 'assets/css/font.css' ?>">
    <link rel="stylesheet" href="<?php echo $baseUrl . 'assets/css/docs.css' ?>">
    <link rel="stylesheet" href="<?php echo $baseUrl . 'assets/css/github-markdown.css' ?>">
    <link rel="stylesheet" href="<?php echo $baseUrl . 'assets/css/highlight.min.css' ?>">
    <link rel="stylesheet" href="<?php echo $baseUrl . 'assets/css/highlight.github.css' ?>">

    <?php
    if ($theme->additionalCss) {
        echo '<style type="text/css">' . $theme->additionalCss . '</style>';
    }
    ?>

    <script src="<?php echo $baseUrl . 'assets/js/highlight.pack.js' ?>"></script>
    <script>hljs.initHighlightingOnLoad();</script>
</head>
<body>

<nav>
    <a id="header" href="<?php echo $baseUrl . 'index.html' ?>"><?php echo $theme->title ?></a>

    <ul>
        <?php
        foreach ($theme->getNavItems() as $navItem) {
            buildNavItem($navItem, $baseUrl, $theme->getCurrentItemId());
        }
        if ($theme->naviLinks) {
            echo '<li class="spacer"></li>';
        }
        foreach ($theme->naviLinks as $label => $url) {
            echo sprintf('<li><a href="%s">%s</a></li>', $url, $label);
        }
        ?>
    </ul>
</nav>

<section>
    <ul class="breadcrumb">
        <?php
        if ($currentItem) {
            foreach ($currentItem->getBreadcrumb() as $item) {
                echo '<li><a href="' . $baseUrl . $item->relUrl . '">' . $item->label . '</a></li>';
            }
        }
        ?>
    </ul>

    <div class="markdown-body">
        <?php
        $content = $theme->getCurrentContent();
        if ($content) {

            // doc page
            echo $content;
        } elseif (!$currentItem) {
            // index page
            echo '<h1>' . $theme->title . '</h1>';

            echo '<p>' . $theme->description . '</p>';

            echo '<ul>';
            foreach ($theme->getNavItems() as $navItem) {
                echo '<li><a href="' . $baseUrl . $navItem->relUrl . '">' . $navItem->label . '</a></li>';
            }
            echo '</ul>';
        } elseif ($currentItem->children) {

            // folder with children
            echo '<h1>' . $currentItem->label . '</h1>';

            echo '<ul>';
            foreach ($currentItem->children as $child) {
                echo '<li><a href="' . $baseUrl . $child->relUrl . '">' . $child->label . '</a></li>';
            }
            echo '</ul>';
        }
        ?>

        <footer>
            <?php
            $prev = $theme->getPreviousNavItem();
            if ($prev) {
                echo '<a class="prev" href="' . $baseUrl . $prev->relUrl . '">‹ ' . $prev->label . '</a>';
            }
            $next = $theme->getNextNavItem();
            if ($next) {
                echo '<a class="next" href="' . $baseUrl . $next->relUrl . '">' . $next->label . ' ›</a>';
            }
            ?>
        </footer>
    </div>
</section>

</body>
</html>
