<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * MDr Update Config
 */
foreach (MDR_CONFIG as $key => $value) $this->options->$key = $value;
if ($this->user->hasLogin() && $this->user->pass('administrator', true) and null !== @$_GET['debug']) {
    if ($_GET['debug'] == 'start') {
        $_SESSION['mdrConfig'] = $_POST;
        header('Location: ' . $this->options->originalSiteUrl . '/?debug=true');
        exit();
    }
    if ($_GET['debug'] == 'true') {
        if (!is_array(@$_SESSION['mdrConfig'])) {
            header('Location: ' . $this->options->originalSiteUrl . '/');
            exit();
        } else foreach ($_SESSION['mdrConfig'] as $name => $key) $this->options->$name = $key;
    }
}
?>
<!DOCTYPE html>
<html <?php if ($this->options->mdrPray) : ?>class="pray" <?php endif; ?> lang="zh-CN">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= $this->options->charset ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="<?= MDR_COLOR['theme'][$this->options->mdrPrimary] ?>">
    <?php if ($this->options->favicon) : ?>
        <link rel="shortcut icon" href="<?= $this->options->favicon ?>" />
    <?php endif; ?>
    <title><?php $this->archiveTitle([
                'category' => _t('分类 %s 下的文章'),
                'search'   => _t('包含关键字 %s 的文章'),
                'tag'      => _t('标签 %s 下的文章'),
                'date'     => _t('在 %s 发布的文章'),
                'author'   => _t('作者 %s 发布的文章')
            ], '', ' - '); ?><?php $this->options->title(); ?><?= ($this->is('index') && $this->options->subTitle) ? ' - ' . $this->options->subTitle : '' ?></title>
    <?php $this->header('generator=&xmlrpc=&wlw=&commentReply=&antiSpam='); ?>
    <link rel="stylesheet" href="<?= staticUrl('mdui.min.css') ?>" />
    <?php if (!$this->options->mdrCornertool) : ?>
        <style>
            *::-webkit-scrollbar { /* Chrome, Safari, Opera */
                width: 0px;
                height: 0px;
            }
            html {
                -ms-overflow-style: none; /* IE, Edge */
                scrollbar-width: none; /* Firefox */
            }
        </style>
    <?php endif; ?>
    <?php if (MDR_DEBUG) : ?>
        <link rel="stylesheet" href="<?php cjUrl('css/style-petals.css?ts=' . time()) ?>" />
        <link rel="stylesheet" href="<?php cjUrl('css/style.css?ts=' . time()) ?>" />
    <?php else: ?>
        <link rel="stylesheet" href="<?php cjUrl('css/style.min.css?v=petals-dev-3') ?>" />
    <?php endif; ?>
    <?php if ($this->options->ViewImg) : ?>
        <link rel="stylesheet" href="<?= staticUrl('jquery.fancybox.min.css') ?>" />
    <?php endif; ?>
</head>

<body class="<?php if (@$_COOKIE['dark'] == '1') : ?>mdui-theme-layout-dark<?php endif; ?>
             <?php if ($this->options->mdrNavDefOpen) : ?>mdui-drawer-body-left<?php endif; ?>
             mdui-appbar-with-toolbar
             mdui-theme-accent-<?= $this->options->mdrAccent ?> 
             mdui-theme-primary-<?= $this->options->mdrPrimary ?>">
    <?php if (!MDR_PJAX) : ?>
        <div class="mdui-progress" <?= 'style="' . (($this->options->mdrLoading == 'bottom') ? 'bottom: 0' : 'top: 0') . '"' ?> id="loading">
            <div class="mdui-progress-indeterminate"></div>
        </div>
        <header class="mdui-appbar mdui-appbar-fixed" style="background: <?= (@$_COOKIE['dark'] == '1') ? '#212121' : '#fff' ?>;z-index:5000;">
            <div class="mdui-toolbar mdui-color-theme">
                <a class="mdui-btn mdui-btn-icon" mdui-drawer="{target: '#mdrDrawerL'}">
                    <i class="mdui-icon material-icons">menu</i>
                </a>
                <a href="<?php $this->options->siteUrl(); ?>" class="mdr-appbar-title">
                    <?php ($this->options->customTitle) ? $this->options->customTitle() : $this->options->title() ?>
                </a>
                <div class="mdui-toolbar-spacer"></div>
                <?php if ($this->options->mdrQrCode) : ?>
                    <div id="pageQrCode" class="mdui-menu" onclick="mdrQrCode.close()"></div>
                    <button class="mdui-btn mdui-btn-icon" id="switchQrCode"><i class="mdui-icon material-icons">phonelink</i></button>
                <?php endif; ?>
            </div>
        </header>
        <aside class="mdui-drawer <?php if (!$this->options->mdrNavDefOpen) : ?>mdui-drawer-close<?php endif; ?>" id="mdrDrawerL">
            <div class="mdui-appbar mdui-hidden-md-up">
                <div class="mdui-toolbar mdui-color-theme"></div>
            </div>
            <div class="mdui-tab mdui-tab-full-width" id="mdrTab">
                <a href="#mdrDrawerLmenu" class="mdui-ripple">菜单</a>
                <a href="#mdrDrawerLtoc" class="mdui-ripple">目录</a>
            </div>
            <div id="mdrDrawerLmenu">
                <ul class="mdui-list" mdui-collapse="{accordion: true}">
                    <form method="post" id="search" action="<?php $this->options->siteUrl(); ?>">
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">搜索</label>
                            <input class="mdui-textfield-input" type="text" id="s" name="s" />
                        </div>
                    </form>
                    <a href="<?php $this->options->siteUrl(); ?>">
                        <li class="mdui-list-item mdui-ripple">
                            <i class="mdui-list-item-icon mdui-icon material-icons <?php if ($this->options->mdrNavColorBut) { ?>mdui-text-color-blue<?php } ?>">home</i>
                            <div class="mdui-list-item-content">首页</div>
                        </li>
                    </a>
                    <?php if (!empty($this->options->Navset) && in_array('ShowCategory', $this->options->Navset)) : ?>
                        <li class="mdui-collapse-item <?= (!empty($this->options->Navset) && in_array('OpenCategory', $this->options->Navset)) ? 'mdui-collapse-item-open' : '' ?>">
                            <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                                <i class="mdui-list-item-icon mdui-icon material-icons <?php if ($this->options->mdrNavColorBut) { ?>mdui-text-color-green<?php } ?>">widgets</i>
                                <div class="mdui-list-item-content">分类</div>
                                <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                            </div>
                            <ul class="mdui-collapse-item-body mdui-list mdui-list-dense">
                                <?php $this->widget('Widget_Metas_Category_List')->to($categorys); ?>
                                <?php while ($categorys->next()) : ?>
                                    <?php if ($categorys->levels == 0) : $children = $categorys->getAllChildren($categorys->mid); ?>
                                        <?php if (empty($children)) : ?>
                                            <a href="<?php $categorys->permalink(); ?>">
                                                <li class="mdui-list-item mdui-ripple"><?php $categorys->name(); ?></li>
                                            </a>
                                        <?php else : ?>
                                            <a href="<?php $categorys->permalink(); ?>">
                                                <li class="mdui-list-item mdui-ripple"><?php $categorys->name(); ?></li>
                                            </a>
                                            <?php foreach ($children as $mid) : $child = $categorys->getCategory($mid); ?>
                                                <a href="<?php echo $child['permalink'] ?>">
                                                    <li class="mdui-list-item mdui-ripple"><?php echo $child['name']; ?></li>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($this->options->Navset) && in_array('ShowPage', $this->options->Navset)) : ?>
                        <li class="mdui-collapse-item <?= (!empty($this->options->Navset) && in_array('OpenPage', $this->options->Navset)) ? 'mdui-collapse-item-open' : '' ?>">
                            <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                                <i class="mdui-list-item-icon mdui-icon material-icons <?php if ($this->options->mdrNavColorBut) { ?>mdui-text-color-deep-orange<?php } ?>">layers</i>
                                <div class="mdui-list-item-content">页面</div>
                                <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                            </div>
                            <ul class="mdui-collapse-item-body mdui-list mdui-list-dense">
                                <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                                <?php while ($pages->next()) : ?>
                                    <a href="<?php $pages->permalink(); ?>">
                                        <li class="mdui-list-item mdui-ripple"><?php $pages->title(); ?></li>
                                    </a>
                                <?php endwhile; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($this->options->MyLinks)) : ?>
                        <li class="mdui-collapse-item mdui-collapse-item-open">
                            <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                                <i class="mdui-list-item-icon mdui-icon material-icons <?php if ($this->options->mdrNavColorBut) { ?>mdui-text-color-purple<?php } ?>">link</i>
                                <div class="mdui-list-item-content">其他</div>
                                <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                            </div>
                            <ul class="mdui-collapse-item-body mdui-list mdui-list-dense">
                                <?php MyLinks($this->options->MyLinks); ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($this->options->Navset) && in_array('ShowArchive', $this->options->Navset)) : ?>
                        <li class="mdui-collapse-item">
                            <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                                <i class="mdui-list-item-icon mdui-icon material-icons <?php if ($this->options->mdrNavColorBut) { ?>mdui-text-color-brown<?php } ?>">archive</i>
                                <div class="mdui-list-item-content">归档</div>
                                <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                            </div>
                            <ul class="mdui-collapse-item-body mdui-list mdui-list-dense">
                                <?php $this->widget('Widget_Contents_Post_Date', 'type=month&format=Y 年 n 月')->parse('<a href="{permalink}"><li class="mdui-list-item mdui-ripple">{date}</li></a>'); ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->options->DarkMode) : ?>
                        <button class="mdui-btn mdui-btn-icon mdui-ripple" id="mdrDarkMode">
                            <i class="mdui-icon material-icons">brightness_4</i>
                        </button>
                    <?php endif; ?>
                </ul>
            </div>
            <div id="mdrDrawerLtoc">
                <ul class="mdui-list"></ul>
            </div>
        </aside>
    <?php endif; ?>
    <main class="mdui-container">
