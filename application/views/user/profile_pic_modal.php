<style>
    .cabinet{
        display: block;
        cursor: pointer;
        position:relative;	
    }
    #upload-sh{
        width: auto;
        height: 250px;
        padding-bottom:25px;
    }

    figure figcaption {
        position: absolute;
        top: 74px;
        color: #fff;
        right:18px;
        padding-right:9px;
        padding-bottom: 5px;
        text-shadow: 0 0 5px #000;
        opacity:0.4;
    }
</style>
<label class="profile-image cabinet" >
    <figure>
        <!--ko with:user-->
             <img  data-bind="attr: {src:(photograph)?'<?php echo base_url().'uploads/'.'organisation_'.$org['id'].'/user_docs/profile_pics/';?>'+photograph:'<?php echo base_url("images/avatar.png");?>' }" class="gambar img-responsive img-thumbnail img-circle " id="item-img-output" height="130px" width="130px" />
        <!--/ko-->
        <figcaption ><i class="fa fa-2x fa-camera" style="right:50px"></i></figcaption>
    </figure>
    <input type="file" class="item-img file center-block " style="display:none;" name="file_photo"/>
</label>

<div class="modal fade" id="cropImagePop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
               
                <center>
                    <small>Zoom in and out to fit your photo in the square, click crop & save.</small>
                </center>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="pull-right">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="upload-sh" class="center-block"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" id="cropImageBtn" class="btn btn-primary">Crop & save</button>
            </div>
        </div>
    </div>
</div>