    <table class="post-list">
        <tr class="head">
            <th>Title</th>
            <th>Created or Published</th>
			<?php if (config("views.counter") == "true"): ?>
                <th>Views</th>
			<?php endif; ?>
            <th>Author</th>
			<?php if (config("input.showTag") == "true"): ?>
                <th>Tags</th>
			<?php endif; ?>			
            <th>Operations</th>
        </tr>
        <?php $i = 0; $len = count($posts); ?>
        <?php foreach ($posts as $p): ?>
            <?php
				if ($i == 0) {
					$class = 'item first';
				} elseif ($i == $len - 1) {
					$class = 'item last';
				} else {
					$class = 'item';
				}
				$i++;
            ?>
            <tr class="<?php echo $class ?>">
                <td><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></td>
                <td><?php echo date('d F Y', $p->date) ?></td>
                <?php if (config("views.counter") == "true"): ?>
                    <td><?php echo $p->views ?></td>
				<?php endif; ?>
                <td><a href="<?php echo $p->authorUrl ?>"><?php echo $p->author ?></a></td>
				<?php if (config("input.showTag") == "true"): ?>
					<td><?php echo $p->tag ?></td>
				<?php endif; ?>
                <td><a href="<?php echo $p->url ?>/edit?destination=<?php echo $actionDestination; ?>">Edit</a> | <a href="<?php echo $p->url ?>/delete?destination=<?php echo $actionDestination; ?>">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
        <div class="pager">
            <?php if (!empty($pagination['prev'])): ?>
                <span><a href="?page=<?php echo $page - 1 ?>" class="pagination-arrow newer" rel="prev">Newer</a></span>
            <?php endif; ?>
            <?php if (!empty($pagination['next'])): ?>
                <span><a href="?page=<?php echo $page + 1 ?>" class="pagination-arrow older" rel="next">Older</a></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>