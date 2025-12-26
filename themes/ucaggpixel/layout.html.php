<?php if (!defined('HTMLY')) die('UCAGG'); ?>
<?php theme_settings(); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language();?>">
<head>
  <?php echo head_contents();?>
  <?php echo $metatags;?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <link href="<?php echo theme_path() ?>css/style.css?v=1" rel="stylesheet"/>
</head>
<body class="<?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">

  <div class="stars"></div>
  <?php if (theme_config('petals')): ?>
    <div class="petals"></div>
  <?php endif; ?>

  <?php if (facebook()) { echo facebook(); } ?>
  <?php if (login()) { toolbar(); } ?>

  <header class="site-header">
    <div class="container">
      <div class="navbar">
        <a class="brand" href="<?php echo site_url(); ?>">
          <div>
            <div class="mark"><?php echo blog_title(); ?></div>
            <div class="sub">DIF-TOR HEH SMUSMA</div>
          </div>
        </a>

        <div class="search">
          <?php search(); ?>
        </div>

        <nav class="nav-links" aria-label="Social links">
          <a class="icon-btn" href="<?php echo theme_config('linkedin_url') ?: 'https://www.linkedin.com/in/diar-kryeziu-23870638a?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app'; ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
            <svg viewBox="0 0 16 16" shape-rendering="crispEdges" aria-hidden="true">
  <rect x="1" y="1" width="14" height="14" fill="none" stroke="white" stroke-width="2"/>
  <rect x="4" y="6" width="2" height="7" fill="white"/>
  <rect x="4" y="4" width="2" height="1" fill="white"/>
  <rect x="7" y="6" width="2" height="7" fill="white"/>
  <rect x="9" y="7" width="2" height="1" fill="white"/>
  <rect x="11" y="6" width="1" height="7" fill="white"/>
  <rect x="9" y="6" width="2" height="1" fill="white"/>
</svg>
          </a>
          <a class="icon-btn" href="<?php echo theme_config('github_url') ?: 'https://github.com/ucagg'; ?>" target="_blank" rel="noopener" aria-label="GitHub">
            <svg viewBox="0 0 16 16" shape-rendering="crispEdges" aria-hidden="true">
  <rect x="1" y="1" width="14" height="14" fill="none" stroke="white" stroke-width="2"/>
  <rect x="4" y="4" width="2" height="2" fill="white"/>
  <rect x="10" y="4" width="2" height="2" fill="white"/>
  <rect x="4" y="6" width="8" height="6" fill="white"/>
  <rect x="6" y="8" width="1" height="1" fill="#0b0b0d"/>
  <rect x="9" y="8" width="1" height="1" fill="#0b0b0d"/>
  <rect x="7" y="10" width="2" height="1" fill="#0b0b0d"/>
</svg>
          </a>
        </nav>
      </div>
    </div>
  </header>

  <div class="wrap">
    <div class="container">
      <div class="site-grid">
        <section id="content">
          <?php echo content(); ?>
        </section>

        <aside class="stack" id="sidebar">

          <?php if (theme_config('show_hub') === null || theme_config('show_hub')): ?>
          <div class="card hub-card" id="hub-card">
            <div class="card-h">
              <h2><?php echo theme_config('hub_title') ?: 'HUB'; ?></h2><div class="hub-tools">  <label class="hub-toggle" title="Hide/Show menu">    <input type="checkbox" id="hubHideToggle" />    <span>HIDE</span>  </label>  <span class="badge">LINKS</span></div>
            </div>
            <div class="card-b">
              <a class="btn" href="<?php echo theme_config('hub_blog') ?: 'https://blogs.ucagg.me'; ?>">
                <img class="ico" src="<?php echo theme_path(); ?>images/<?php echo theme_config('hub_blog_icon') ?: 'ucagg_blog_logo_1024.png'; ?>" alt="<?php echo theme_config('hub_blog_label') ?: 'BLOG'; ?> icon">
                <span class="label"><?php echo theme_config('hub_blog_label') ?: 'BLOG'; ?></span>
                <span class="dot" aria-hidden="true"></span>
              </a>
              <div style="height:10px"></div>
              <a class="btn" href="<?php echo theme_config('hub_cloud') ?: 'https://cloud.ucagg.me'; ?>">
                <img class="ico" src="<?php echo theme_path(); ?>images/<?php echo theme_config('hub_cloud_icon') ?: 'ucagg_cloud_logo_1024.png'; ?>" alt="<?php echo theme_config('hub_cloud_label') ?: 'CLOUD'; ?> icon">
                <span class="label"><?php echo theme_config('hub_cloud_label') ?: 'CLOUD'; ?></span>
                <span class="dot" aria-hidden="true"></span>
              </a>
              <div style="height:10px"></div>
              <a class="btn" href="<?php echo theme_config('hub_files') ?: 'https://files.ucagg.me'; ?>">
                <img class="ico" src="<?php echo theme_path(); ?>images/<?php echo theme_config('hub_files_icon') ?: 'ucagg_files_logo_1024.png'; ?>" alt="<?php echo theme_config('hub_files_label') ?: 'FILES'; ?> icon">
                <span class="label"><?php echo theme_config('hub_files_label') ?: 'FILES'; ?></span>
                <span class="dot" aria-hidden="true"></span>
              </a>
              <div style="height:10px"></div>
              <a class="btn" href="<?php echo theme_config('hub_games') ?: 'https://games.ucagg.me'; ?>">
                <img class="ico" src="<?php echo theme_path(); ?>images/<?php echo theme_config('hub_games_icon') ?: 'ucagg_games_logo_1024.png'; ?>" alt="<?php echo theme_config('hub_games_label') ?: 'GAMES'; ?> icon">
                <span class="label"><?php echo theme_config('hub_games_label') ?: 'GAMES'; ?></span>
                <span class="dot" aria-hidden="true"></span>
              </a>
              <div style="height:10px"></div>
              <a class="btn" href="<?php echo theme_config('hub_projects') ?: 'https://projects.ucagg.me'; ?>">
                <img class="ico" src="<?php echo theme_path(); ?>images/<?php echo theme_config('hub_projects_icon') ?: 'ucagg_projects_logo_1024.png'; ?>" alt="<?php echo theme_config('hub_projects_label') ?: 'PROJECTS'; ?> icon">
                <span class="label"><?php echo theme_config('hub_projects_label') ?: 'PROJECTS'; ?></span>
                <span class="dot" aria-hidden="true"></span>
              </a>
            </div>
          </div>
          <?php endif; ?>

          <?php if (theme_config('recent_posts')): ?>
          <div class="card">
            <div class="card-h"><h2><?php echo i18n('Recent_posts'); ?></h2><span class="badge">NEW</span></div>
            <div class="card-b widget-list"><?php echo recent_posts(); ?></div>
          </div>
          <?php endif; ?>

          <?php if (theme_config('popular_posts')): ?>
          <div class="card">
            <div class="card-h"><h2><?php echo i18n('Popular_posts'); ?></h2><span class="badge">TOP</span></div>
            <div class="card-b widget-list"><?php echo popular_posts(); ?></div>
          </div>
          <?php endif; ?>

          <?php if (theme_config('category_list')): ?>
          <div class="card">
            <div class="card-h"><h2><?php echo i18n('Categories'); ?></h2><span class="badge">ALL</span></div>
            <div class="card-b widget-list"><?php echo category_list(); ?></div>
          </div>
          <?php endif; ?>

          <?php if (theme_config('tagcloud')): ?>
          <div class="card">
            <div class="card-h"><h2><?php echo i18n('Tags'); ?></h2><span class="badge">TAG</span></div>
            <div class="card-b"><div class="tag-cloud"><?php echo tag_cloud(); ?></div></div>
          </div>
          <?php endif; ?>

        </aside>
      </div>

      <footer class="site-footer">
        <div class="footer-row">
          <?php $cp = blog_copyright(); $ft = theme_config('footer_text'); ?>
          <span><?php echo !empty($cp) ? $cp : (!empty($ft) ? $ft : ('© ' . date('Y') . ' ucagg.me · UCAGG — signal over noise')); ?></span>
          <span><a href="<?php echo site_url(); ?>">Home</a></span>
        </div>
      </footer>
    </div>
  </div>

  <script src="<?php echo theme_path(); ?>js/effects.js"></script>
  <?php if (analytics()): ?><?php echo analytics(); ?><?php endif; ?>
</body>
</html>
