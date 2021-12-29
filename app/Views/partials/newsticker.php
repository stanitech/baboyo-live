
<?php if(count($announcements) > 0):?>
<link rel='stylesheet' href='/assets/news-ticker/ticker.css'>
<div class="ticker-container mb-0">
  <div class="ticker-caption"><p>Announcements</p></div>
  <ul class='small'>
    <?php foreach ($announcements as $key):?>
    <div>
        <li><span><strong class='text-uppercase'><?=$key->name?></strong> &ndash; <?=ellipsize(strip_tags($key->description),150)?> <a href="/news/<?=$key->slug?>" target="_blank">read more</a></span></li>
    </div>
    <?php endforeach?>
  </ul>
</div>
<script src="/assets/news-ticker/ticker.js"></script>
<?php endif?>