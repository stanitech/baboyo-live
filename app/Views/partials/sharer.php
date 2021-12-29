<div class="btn-group">
    <?php
        $socials = [
            ["name"=>"Facebook","icon"=>"fa-facebook-f","link"=>'https://www.facebook.com/sharer.php?u='.urlencode(current_url()).'&t='.urlencode($title)
            ],
            ["name"=>"Twitter","icon"=>"fa-twitter","link"=>'https://twitter.com/intent/tweet?url='.urlencode(current_url()).'&text='.urlencode($title)
            ],
            ["name"=>"Whatsapp","icon"=>"fa-whatsapp","link"=>'whatsapp://send?text='.urlencode(current_url())
            ],
            ["name"=>"Email","icon"=>"fa fa-envelope","link"=>'mailto:?subject='.urlencode($title).'&body='.urlencode("Check this out")
            ],
        ];
        foreach ($socials as $key):?>
        <a v-b-tooltip title="<?=$key['name']?>" href="" onclick="window.open('<?=$key['link']?>','popup','width=600,height=600');return false" class="share-btn"><i class="fab <?=$key['icon']?>"></i></a>
        <?php endforeach?>
</div>