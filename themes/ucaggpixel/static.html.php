<?php if (!defined('HTMLY')) die('UCAGG'); ?>
<div class="card">
  <?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
  <?php endif; ?>
  <?php if (login()) { echo tab($p); } ?>

  <div class="article">
    <h1><?php echo $p->title; ?></h1>
    <div class="post-body">
      <?php echo $p->body; ?>
    </div>
  </div>
</div>
