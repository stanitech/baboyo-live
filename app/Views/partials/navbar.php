<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Baboyo</a>
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                <li class="nav-item <?=strpos(current_url(),'/livescores/') == true? 'active':''?>">
                    <a class="nav-link" href="/livescores/<?=date("Y-m-d")?>">Livescores </a>
                </li>
                <li class="nav-item <?=strpos(current_url(),'/my-predictions/') == true? 'active':''?>">
                    <a class="nav-link" href="/my-predictions/<?=date("Y-m-d")?>">My Predictions</a>
                </li>
             
            </ul>
        </div>
    </div>
</nav>