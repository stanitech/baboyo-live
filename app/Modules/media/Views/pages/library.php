<?= $this->extend("layouts/backend") ?>
<?= $this->section("content") ?>
<section class="section">
    <b-overlay :show="isLoading">
        <template #overlay> Please wait...<br>
            <progress></progress>
        </template>
        <b-sidebar v-if="currentFile" body-class="card card-body" v-model="showFileDetails" width="100%" shadow backdrop bg-variant="white" no-header>
            <b-overlay :show="isLoading">
                <template #overlay> Please wait...<br>
                    <progress></progress>
                </template>
                <div class="row">
                    <div class="col-lg-5 offset-md-1 mb-3 d-flex justify-content-center">
                        <media-thumbnail :size="'100%'" :item="currentFile" v-if="currentFile.type.includes('image')"></media-thumbnail>
                        <audio-thumbnail v-else-if="currentFile.type.includes('audio')" :item="currentFile"></audio-thumbnail>
                    </div>
                    <div class="col-lg-5 offset-md-1">
                        <ul class="list-unstyled">
                            <li class=""><strong>Uploaded On</strong>: {{currentFile.created_at}}</li>
                            <!-- <li class="mb-2"><strong>Uploaded by</strong>: </li> -->
                            <li class=" d-flex align-items-center"><strong class="mr-1">File name: </strong>
                                <form action="" method="POST" class="form-material m-0" @submit="isLoading = true">
                                    <div class="form-group m-0">
                                        <input type="text" name="name" class="form-control m-0 p-0" v-model="currentFile.name">
                                        <input type="hidden" name="id" :value="currentFile.id">
                                        <input type="hidden" name="_method" value="PUT" />
                                    </div>
                                </form>
                            </li>
                            <li class="mb-2"><strong>File type</strong>: {{currentFile.type}}</li>
                            <li class="mb-2"><strong>File size</strong>: {{(currentFile.size /1000000).toFixed(2) }} MB</li>
                            <li class="mb-2 "><strong>File URL</strong>: {{currentFile.file}}</li>

                        </ul>
                        <div class="d-flex justify-content-between">
                            <form action="" method="POST" enctype="multipart/form-data" ref="change-file" @submit="isLoading = true">
                                <input type="hidden" name="id" :value="currentFile.id">
                                <label for="change-file" class="btn btn-link btn-sm small p-0">
                                    <input @change="$refs['change-file'].submit()" id="change-file" required name="file" type="file" class="d-none" />Change File
                                </label>
                            </form>
                            <form action="" method="POST" @submit.prevent="handleDelete($event)">
                                <input type="hidden" name="id" :value="currentFile.id">
                                <input type="hidden" name="_method" value="DELETE" />
                                <button type="submit" class="btn btn-link text-danger btn-sm small p-0">Delete Permanently</button>
                            </form>
                        </div>
                        <div class="form-group mt-5">
                            <button type="button" @click.prevent="showFileDetails=false;currentFile=null" class="btn btn-light mr-3">Close</button>
                        </div>
                    </div>
                </div>
            </b-overlay>
        </b-sidebar>
        <div class="d-flex justify-content-between">
            <h1 class="mb-2 text-primary">Library</h1>
            <button class="btn btn-primary text-uppercase" @click.prevent="addFile = !addFile"><i class="icon-add-3 mr-2"></i> Add File</button>
        </div>
        <p>Manage all media files</p>
        <div class="row ">
            <div class="col-12 mb-5" v-show="addFile">
                <div class="card card-body">
                    <b-tabs pills align='center'>
                        <b-tab title="Insert Media">
                            <image-uploadify @on-upload="reload($event)" />
                        </b-tab>
                        <b-tab title="Insert From URL">
                            <form action="/administrator/media" class="form-material " method="POST">
                                <input type="hidden" name="source" value="EXTERNAL">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="type" :value="file.type">
                                <div class="form-group form-float">
                                    <label class="form-label">File URL</label>
                                    <div class="form-line">
                                        <input class="form-control" type="url" name="file" placeholder="https://" v-model="file.url">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">File Name</label>
                                    <div class="form-line">
                                        <input class="form-control" type="text" name="name" v-model="file.name">
                                    </div>
                                </div>
                                <div class="form-group d-flex justify-content-end">

                                    <button class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </b-tab>
                    </b-tabs>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0">
                            <h4 class="text-primary">My Drive</h4>
                        </li>
                        <a href="/administrator/media" class="list-group-item list-group-item-action">All Files</a>
                        <a href="/administrator/media?type=application" class="list-group-item list-group-item-action">Documents</a>
                        <a href="/administrator/media?type=image" class="list-group-item list-group-item-action">Images</a>
                        <a href="/administrator/media?type=video" class="list-group-item list-group-item-action">Video</a>
                        <a href="/administrator/media?type=audio" class="list-group-item list-group-item-action">Audios</a>
                        <a href="/administrator/media?type=zip" class="list-group-item list-group-item-action">Zip Files</a>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card card-body">
                    <form class="input-group border">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent border-0"><i class="icon-search-1"></i></span>
                        </div>
                        <input value="<?=service('request')->getGet('search')?>" name="search" class="form-control border-0" type="search" placeholder="Search the files">
                    </form>
                    <div class="row mt-3 has-items-overlay">
                        <?php if (count($files->media) > 0) : ?>
                            <?php foreach ($files->media as $file) : ?>
                                <div class="col-6 col-md-4 col-xl-3" @click.prevent='openFileDetails(<?= json_encode($file) ?>)'>
                                    <figure class="shadow">
                                        <div class="img-wrapper ">
                                            <img src=<?php
                                                        if (strpos($file->type, "image/") !== false) {
                                                            echo $file->file;
                                                        } elseif (strpos($file->type, "audio/") !== false) {
                                                            echo env("app.audio.placeholder");
                                                        }

                                                        ?> alt="/" style="width:100%;height:10rem;object-fit:cover">


                                            <div class="img-overlay text-white">
                                                <div class="figcaption">
                                                    <div class="text-center">
                                                        <h6><?= $file->name ?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="figure-title text-center p-2">
                                                <h6 class="w-100 text-truncate"><?= $file->name ?></h6>
                                            </div>
                                        </div>
                                    </figure>
                                </div>
                            <?php endforeach ?>
                            <div class="col-12 d-flex justify-content-end">
                                <?= $files->pager->links() ?>
                            </div>
                        <?php else : ?>
                            <div class="col-12 text-center mt-5">
                                <h2>No file found</h2>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </b-overlay>
</section>
<script>
    new Vue({
        el: ".section",
        data: {
            isLoading: false,
            addFile: false,
            showFileDetails: false,
            currentFile: null,
            file: {
                url: "",
                name: '',
                type: '',
            }

        },
        watch: {
            'file.url'(value) {
                if (value.endsWith(".mp3")) {
                    this.file.type = "audio/mpeg"
                } else if (value.endsWith(".jpg") || value.endsWith(".png") || value.endsWith(".webp") || value.endsWith(".jpeg") || value.endsWith(".gif") || value.endsWith(".tiff") || value.endsWith(".svg")) {
                    this.file.type = "image/jpeg"
                } else {
                    this.file.type = ""
                }
            }
        },
        computed: {
            size() {
                if (!this.detail.size) {
                    return 0;
                }
                return numberToSize(this.detail.size)
            }
        },
        methods: {
            openFileDetails(file) {
                this.currentFile = file;
                this.showFileDetails = true;
                window.scrollTo(0, 0)

            },
            reload(e) {
                if (e && e.status) {
                    this.isLoading = true;
                    this.addFile = false;
                    window.location.reload();
                }
            },
            handleDelete(e) {
                this.$bvModal.msgBoxConfirm('Please confirm that you want to delete', {
                    title: 'Please Confirm',
                    size: 'sm',
                    headerClass: 'p-2 border-bottom-0',
                    footerClass: 'p-2 border-top-0',
                    centered: true,
                    static: true,
                    buttonSize: 'sm'
                }).then(value => {
                    if (value) {
                        this.isLoading = true,
                            e.target.submit();
                    }
                })
            },
        }
    })
</script>
<?= $this->endSection() ?>