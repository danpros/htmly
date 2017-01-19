<h1 class="page-header">Overview</h1>
<div class="col-md-6 table-responsive">
    <h2 class="sub-header">Your recent posts</h2>
    <?php
        get_user_posts();
    ?>
</div>
<div class="col-md-6 table-responsive">
    <h2 class="sub-header">Static pages</h2>
    <?php
        get_user_pages(); 
    ?>
</div>