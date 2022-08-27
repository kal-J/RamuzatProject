<style>
    .cabinet{
        display: block;
        cursor: pointer;
        position:relative;	
        max-width:500px;
    }
    #upload-sh_sign{
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
    .bg-placeholder{
        background: repeating-linear-gradient(
        135deg,
        #ccc,
        #ccc 1px,
        #eee 1px,
        #eee 2px
        );
}
 .faded {
    opacity: 0.3;
    padding:5px;
    display:inline;
}
 .faded:hover {
    opacity: 1;
    background-color: Black;
}
</style>
<?php if(in_array('1', $member_staff_privilege)){ ?> 
<label class=" cabinet" >
    <figure> 
            <!--ko foreach:signature -->
           <!-- ko if: ($index() === 0) -->
                 <img  data-bind="attr: {src:(signature)?('<?php echo base_url().'uploads/'.'organisation_'.$org['id'].'/user_docs/signatures/';?>'+signature ):'' }" class="thumbnail img-responsive " id="item-img-output_sign" height="200px" width="400px" />
           <!--/ko-->
           <!--/ko-->
           <!--ko ifnot:$root.signature().length -->
                <div class="bg-placeholder" id="sign_pic_result" style="height:200px; width:auto;"><br><br>
                <center><h2> CLICK TO UPLOAD <br>A  SIGNATURE</h2></center>
                </div> 
          <!--/ko-->
            <figcaption ><i class="fa fa-2x fa-camera" style="right:50px"></i></figcaption> 
    </figure>
    <input type="file" class="item-img_sign file center-block"  style="display:none;" name="file_photo"/>
</label>
<div style="max-width:540px;" >
<!--ko foreach:signature -->
   <!-- ko if: ($index() !== 0) -->
         <div class="faded" style="height:100px; width:200px;" >
             <img  data-bind="attr: {src:(signature)?('<?php echo base_url().'uploads/'.'organisation_'.$org['id'].'/user_docs/signatures/';?>'+signature ):'',title:(date_created)?moment(date_created,'X').format('D-MMM-YYYY'):''}" class=" img-responsive " id="item-img-output_sign" style="height:100px; width:200px;" />
         </div>
     <!--/ko-->
  <!--/ko-->
  </div>
<?php }?>
<div class="modal fade" id="signatureCropPop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <center>
                    <small>Zoom in and out to fit your photo in the square, click crop & save.</small>
                </center>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="pull-right">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="upload-sh_sign" class="center-block"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" id="cropImageBtn_sign" class="btn btn-primary">Crop & save</button>
            </div>
        </div>
    </div>
</div>