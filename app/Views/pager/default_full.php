<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Page navigation" class="bettix-pagination">
    <ul>
    <?php if ($pager->hasPrevious()) : ?>
        <li class="">
            <a class="" href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">
                <span aria-hidden="true"><?= lang('Pager.first') ?></span>
            </a>
        </li>
        <li class="">
            <a class="" href="<?= $pager->getPrevious() ?>">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    <?php endif ?>

    <?php foreach ($pager->links() as $link) : ?>
        <li >
            <a class=" <?= $link['active'] ? 'active' : '' ?>" href="<?= $link['uri'] ?>">
                <?= $link['title'] ?>
            </a>
        </li>
    <?php endforeach ?>

    <?php if ($pager->hasNext()) : ?>
        <li class="">
            <a class="" href="<?= $pager->getNext() ?>" >
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
        <li class="">
            <a class="" href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.last') ?>">
                <span aria-hidden="true"><?= lang('Pager.last') ?></span>
            </a>
        </li>
    <?php endif ?>
    </ul>
</nav>

