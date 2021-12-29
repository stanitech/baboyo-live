<?= $this->extend("layouts/frontend") ?>
<?= $this->section("content") ?>

<div class="betting pt-4 min-50vh"
    style="background:url(/assets/img/banner-1.jpg);background-position:center center;background-size:100%;background-attachment: fixed;background-repeat: no-repeat;background-blend-mode: overlay;background-color:#ffffffdb;background-origin: border-box">

</div>

<div class="standing p-0">

    <div class="standing-list-cover">
        <div class="standing-team-list">
            <h6 class="text-right">*Rankings was updated at 2021-11-05 12:01:53</h6>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th colspan="2">Points</th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($leagues as $key => $league) :?>
                    <tr>
                        <th scope="row"><?= ++$key ?></th>
                        <td colspan="2">
                            <span class="single-team">
                                <span class="logo">
                                    <img src="<?= $league->img ?>" alt="">
                                </span>
                                <span class="text">
                                    <a href="/team-single"><?= $league->name ?></a>
                                </span>
                            </span>
                        </td>
                        <td><?= $league->point ?></td>

                    </tr>
                    <?php endforeach ?>


                </tbody>
            </table>
        </div>
    </div>

</div>
</div>


<?= $this->endSection() ?>